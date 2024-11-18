<template>
    <div>
        <h1>Access Restricted</h1>
        <p>Your profile is awaiting approval by the administrator to allow you to log in to the dashboard.</p>
        <router-link to="/">Return to Home</router-link>
    </div>
</template>

<script>
    export default {
        name: 'AccessRestricted',
    };
</script>
