// useMatchmakers.js
import { ref } from 'vue';
import axios from 'axios';

export default function useMatchmakers() {
  const matchmakers = ref([]);

  const getMatchmakers = async () => {
    try {
      let response = await axios.get('/api/matchmakers');
      matchmakers.value = response.data.data;
    } catch (error) {
      console.error('Error fetching matchmakers:', error);
    }
  };

  return {
    matchmakers,
    getMatchmakers,
  };
}
