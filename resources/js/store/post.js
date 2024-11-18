export default {
    namespaced: true,
    state:{
        postLiked: false,        
    },
    getters:{
        postLiked(state){
            return state.postLiked
        }
    },
    mutations:{
        SET_LIKED (state, value) {
            state.postLiked = value
        },
    },
    actions:{
        liked({commit}){
            commit('SET_LIKED', true)                        
        },
        unLiked({commit}){
            commit('SET_LIKED', false)            
            
        }        
    }
}