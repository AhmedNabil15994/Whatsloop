<template>
    <div class="home">
        <h2>Page</h2>
    </div>
</template>
<script>
export default {
    name:"page-style",
    data() {
        return {
            token:null
        }
    },
    mounted () {
        if (localStorage.getItem("token") === null) {
            this.$router.push("/");
        } else {
            this.token = localStorage.getItem('token');
        }
    }
}
</script>
<style>
    
</style>