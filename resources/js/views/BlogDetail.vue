<template>
    <div class="mx-auto max-w-7xl py-6 sm:px-6 lg:px-8 items-center justify-center">
        <div class="bg-connectyed-body-light mx-full">                                             
            <article class="mx-full shadow-connectyed rounded-xl bg-white border border-gray-200 flex flex-col py-5 px-5 mb-5">
                <div class="flex text-xs items-end">
                    <time time datetime="posts.created_at" class="text-gray-500 float-end">{{  }}</time>
                </div>
                <div class="group relative">
                    <h2 class="text-3xl font-semibold leading-6 text-gray-900 group-hover:text-gray-600">                        
                        {{ posts.title }}                        
                    </h2>                    
                    <div class="my-3 text-sm leading-6">
                        <p class="font-semibold text-gray-900">
                        <a href="#">
                            <span class="inset-0"></span>
                            {{ posts.name }}
                        </a>
                        </p>                        
                    </div>
                    <div class="text-xl my-5 text-justify" v-html="posts.content">
                    </div>                                                  
                </div>                               
            </article>         
            
            
            
        </div>
    </div>
</template>
  
<script>
import { mapActions, useStore } from 'vuex'
import usePosts from "../composables/posts";


export default {
    name:'postdetail',
    data() {
        return {
            postId: 0,
            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            post: {},
            posts: {},        
            postUrl: "",
            liked: false,
            user:this.$store.state.auth.user,
            authorization:this.$store.state.auth.authorization
        }
    },
    async created() {
        const slug = this.$route.params.slug
        let url = "/api/post/"
        await axios.get(url.concat(slug)).then(({data})=>{
            this.posts = data.data
        })
        .catch(function (error) {
           console.error(error);
        });

    },
    computed() {
    },
    methods: {
        ...mapActions({
            toDetail:"/"
        }),        
    }
} 
</script>