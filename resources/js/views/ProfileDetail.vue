<template>  
    <matchmakerProfileCard :profile="profile" v-if="isMatchmaker" /> 
    <clientProfileCard :profile="profile" v-if="!isMatchmaker" />     
</template>

<script>
import matchmakerProfileCard from './Matchmaker/ProfileCard.vue';
import clientProfileCard from './Client/ProfileCard.vue';

export default {
    name: "profiledetail",
    components: {
        matchmakerProfileCard,
        clientProfileCard
    },
    data(){
      return {        
        processing: false,        
        profile: [],
        currentAvatar: '',
        isMatchmaker: false,
        matchmakerProfile: {
            name: '',
            jobTitle: '',
            description: '',
            email: '',
            image: '',
            twitter: '',
            linkedin: '',
        }        
      }
    },
    mounted() {
        this.getProfile();
    },
    methods: {                        
        async getProfile() {
            this.processing = true            
            const username = this.$route.params.username
            await axios.get('/api/profile/getdetail/'+username)
                .then((response) => {         
                    if (response.data) {                        
                        const profile = response.data.data.profile
                        this.currentAvatar = profile.avatar
                        this.profile = profile
                        this.profile.clients = response.data.data.profile_list ?? []
                        this.profile.specialties = response.data.data.specialties ?? []
                        this.profile.parsedLocations = (response.data.data.specialties) ? JSON.parse(response.data.data.specialties.locations) : []
                        this.isMatchmaker = (profile.user.role === 'matchmaker' || profile.user.role === 'candidate') 
                    } else {                        
                        this.$router.push({ name: "notfound" });
                    }
                                       
            }).catch((error)=>{                
                console.log(error)
            }).finally(()=>{
                this.processing = false
            })
        }
    },
}
</script>