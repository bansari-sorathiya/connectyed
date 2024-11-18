import { ref } from 'vue'

export default function usePosts() {
    const featuredPosts = ref([])        
 
    const getFeaturedPosts = async () => { 
        await axios.get('/api/post/featured')
            .then(response => {
                featuredPosts.value = response.data;                
            })
    }

    return {
        featuredPosts,        
        getFeaturedPosts,            
    } 
}