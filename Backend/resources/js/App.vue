<template>
 <div id="app">
    <!-- slide -->
    <transition name="fade" >
      <home></home>
    </transition>
 </div>
</template>

<script>
import Home from './components/site/home.vue';


export default {
  name: 'App',
  created() {
    window.addEventListener("scroll", this.handleScroll);

    var x = window.matchMedia("(max-width: 991px)")
      if (x.matches) { // If media query matches
        window.location.replace("https://whatsloop.net/app/mobilelivechat/");
      }

  


  },
  destroyed() {
    window.removeEventListener("scroll", this.handleScroll);
  },
  methods: {
    handleScroll () {
      this.$store.dispatch("setCurrentScrollY", window.scrollY);
    }
  } ,
  components:{
        Home
    }
}

</script>
