// resources/js/store/auth.js

import axios from 'axios';
import router from '@/router';
import store from '@/store';

export default {
  namespaced: true,
  state: {
    authenticated: false,
    // For compatibility with both old and new components
    user: null,
    userData: {
      user: null
    },
    authorization: {},
    userRole: null,
    authError: null,
    initialized: false
  },
  getters: {
    authenticated(state) {
      return state.authenticated;
    },
    user(state) {
      // For old components - direct access
      return state.user;
    },
    // For new components - nested access
    nestedUserData(state) {
      return {
        user: state.user  // This maintains the nested structure they expect
      };
    },
    userRole(state) {
      return state.userRole;
    },
    userProfile(state) {
      return state.user?.profile || {};
    },
    authError(state) {
      return state.authError;
    },
    isInitialized(state) {
      return state.initialized;
    }
  },
  mutations: {
    SET_AUTHENTICATED(state, value) {
      state.authenticated = value;
    },
    SET_USER(state, value) {
      // Update both user references to maintain compatibility
      state.user = value;
      state.userData.user = value;
      state.userRole = value?.role;
    },
    SET_TOKEN(state, value) {
      state.authorization = value;
    },
    CLEAR_USER(state) {
      state.user = null;
      state.userData.user = null;
      state.userRole = null;
      state.authorization = {};
    },
    SET_USER_ROLE(state, value) {
      state.userRole = value;
    },
    SET_AUTH_ERROR(state, error) {
      state.authError = error;
    },
    SET_INITIALIZED(state, value) {
      state.initialized = value;
    }
  },
  actions: {
    async initialize({ commit, dispatch }) {
      try {
        const localUser = localStorage.getItem('user');
        if (localUser) {
          const data = JSON.parse(localUser);
          if (data.data?.user && data.authorization?.token) {
            commit('SET_USER', data.data.user);
            commit('SET_AUTHENTICATED', true);
            commit('SET_TOKEN', data.authorization);
            commit('SET_USER_ROLE', data.data.user.role);

            axios.defaults.headers.common['Authorization'] = `Bearer ${data.authorization.token}`;

            // Refresh user data but don't wait for it
            dispatch('getUser').catch(error => {
              console.warn('Background user refresh failed:', error);
            });
          } else {
            throw new Error('Invalid user data structure');
          }
        } else {
          try {
            const { data } = await axios.get('/api/user/session-check');
            if (data && data.user && data.token) {
              const storedData = {
                data: {
                  user: data.user,
                },
                authorization: {
                  token: data.token,
                },
              };
              localStorage.setItem('user', JSON.stringify(storedData));
              commit('SET_USER', data.user);
              commit('SET_AUTHENTICATED', true);
              commit('SET_TOKEN', { token: data.token });
              commit('SET_USER_ROLE', data.user.role);
              axios.defaults.headers.common['Authorization'] = `Bearer ${data.token}`;
            } else {
              commit('CLEAR_USER');
            }
          } catch (error) {
            console.error('Session check failed:', error);
            commit('CLEAR_USER');
          }
        }
      } catch (error) {
        console.error('Error during initialization:', error);
        commit('CLEAR_USER');
      } finally {
        commit('SET_INITIALIZED', true);
      }
    },

    async login({ commit }, credentials) {
      try {
        const { data } = await axios.post('/api/user/login', credentials);
        if (data && data.data && data.data.user && data.authorization && data.authorization.token) {
          localStorage.setItem("user", JSON.stringify(data));
          commit('SET_USER', data.data.user);
          commit('SET_AUTHENTICATED', true);
          commit('SET_TOKEN', data.authorization);
          commit('SET_USER_ROLE', data.data.user.role);
          axios.defaults.headers.common['Authorization'] = `Bearer ${data.authorization.token}`;

          const rolePathMap = {
            'admin': '/admin/dashboard',
            'matchmaker': '/matchmaker/dashboard',
            'candidate': '/client/dashboard',
            'client': '/client/dashboard',
          };
          const rolePath = rolePathMap[data.data.user.role] || '/';
          router.push({ path: rolePath });
        } else {
          throw new Error('Invalid login response structure');
        }
      } catch (error) {
        commit('CLEAR_USER');
        commit('SET_AUTHENTICATED', false);
        commit('SET_AUTH_ERROR', error.response?.data.message || 'Login failed');
        console.error('Login error:', error.response?.data || error.message || error);
        throw error;
      }
    },

    async register({ commit }, userData) {
      try {
        const response = await axios.post('/api/user/register', userData);
        if (response.data.success === true) {
          return response.data;
        } else {
          throw new Error(response.data.message || 'Registration failed');
        }
      } catch (error) {
        throw error;
      }
    },

    logout({ commit }) {
      commit('CLEAR_USER');
      commit('SET_AUTHENTICATED', false);
      localStorage.removeItem("user");
      localStorage.removeItem("vuex");
      delete axios.defaults.headers.common['Authorization'];

      document.cookie = `token=; path=/; domain=connectyed.com; secure; samesite=lax; expires=Thu, 01 Jan 1970 00:00:00 GMT`;

      router.push({ path: '/login' });
    },

    setAuth({ commit }, payload) {
      try {
        if (payload.user && payload.authorization && payload.authorization.token) {
          const storedData = {
            data: { user: payload.user },
            authorization: payload.authorization,
          };
          localStorage.setItem("user", JSON.stringify(storedData));
          commit('SET_USER', payload.user);
          commit('SET_AUTHENTICATED', true);
          commit('SET_TOKEN', payload.authorization);
          commit('SET_USER_ROLE', payload.user.role);
          axios.defaults.headers.common['Authorization'] = `Bearer ${payload.authorization.token}`;
        } else {
          throw new Error('Invalid payload structure for setAuth');
        }
      } catch (error) {
        console.error('Error setting authentication state:', error);
        commit('CLEAR_USER');
      }
    },

    async getUser({ commit }) {
      try {
        const { data } = await axios.get('/api/user/introspect');
        if (data.success && data.data?.user) {
          commit('SET_USER', data.data.user);
          return data.data.user;
        } else {
          throw new Error('Failed to fetch user data');
        }
      } catch (error) {
        console.error('Error fetching user data:', error);
        throw error;
      }
    }
  }
};

// Axios interceptor to auto-logout on 401 errors
axios.interceptors.response.use(
  response => response,
  error => {
    if (error.response && error.response.status === 401) {
      store.dispatch('auth/logout');
    }
    return Promise.reject(error);
  }
);