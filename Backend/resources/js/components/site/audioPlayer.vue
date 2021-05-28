<template>
    <div id="audio-player-root" >

            <!-- Hide the default audio player -->
            <div >
                <audio
                    style="display:none"
                    ref="player"
                    :id="playerid"
                    class="audioPlay"
                >
                    <source :src="url" type="audio/mpeg" />
                </audio>
            </div>
                
            <div
                class="clearfix"
                style="margin: auto;"
            >
                <div id="player-row" class="inline-flex flex-wrap w-full max-w-5xl">
                    <div id="button-div">
                        <svg  
                            @click="toggleAudio()"
                            v-if="!isPlaying"
                            class="play-button text-gray-400"
                            :class="{ 'text-orange-600': audioLoaded, 'hover:text-orange-400': audioLoaded, 'cursor-pointer': audioLoaded }"
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"
                                clip-rule="evenodd"
                            />
                        </svg>
                        <svg
                            @click="toggleAudio()"
                            v-if="isPlaying"
                            class="play-button text-orange-400 hover:text-orange-400 cursor-pointer"                            
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zM7 8a1 1 0 012 0v4a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v4a1 1 0 102 0V8a1 1 0 00-1-1z"
                                clip-rule="evenodd"
                            />
                        </svg>
                    </div>

                    <div id="progress-bar">
                        <div class="rangeStyle" :class=" !audioLoaded ? 'loaded' : ''">
                        
                          <span class="rangeBg" :style="'width:'+ rangeBg +'%'"></span>
                            <input
                                v-model="playbackTime"
                                type="range"
                                min="0"
                                :max="audioDuration"
                                class="slider w-full h-full"
                                id="position"
                                name="position"
                            />
                        </div>
                            <!-- Show loading indicator until audio has been loaded -->
                            
                            <div v-if="!audioLoaded"
                            class="spinner fa fa-spinner fa-pulse"
                            style="color: #889a7b">
                            </div>
                            
                            <div
                                v-if="audioLoaded"
                                class="clearfix times"
                            >
                            
                                <span class="text-sm" style="color: #000" v-html="elapsedTime()"> 00:00 </span>
                                /
                                <span class="text-sm" style="color: #000" v-html="totalTime()"> 00:00 </span>
                                
                            </div>

                            </div>
                        </div>
            </div>
            
            <!-- outer gray border -->
    </div>
    <!-- root -->
</template>

<script>
//import { mapState } from 'vuex'
export default {
    props: ["url", "playerid","pauseall"],
    /**
     * playbackTime = local var that syncs to audio.currentTime
     * audioDuration = duration of audio file in seconds
     * isPlaying = boolean (true if audio is playing)
     *
     **/
    data() {
        return {
            playbackTime: 0,
            audioDuration: 100,
            audioLoaded: false,
            isPlaying: false,
            rangeBg:0
        };
    },
    methods: {
        //Set the range slider max value equal to audio duration
        initSlider() {
            var audio = this.$refs.player;
            if (audio) {
                this.audioDuration = Math.round(audio.duration);
                
            }
        },
        //Convert audio current time from seconds to min:sec display
        convertTime(seconds){
                            const format = val => `0${Math.floor(val)}`.slice(-2);
                var minutes = (seconds % 3600) / 60;
                return [minutes, seconds % 60].map(format).join(":");
        },
        //Show the total duration of audio file
        totalTime() {
            var audio = this.$refs.player;
            if (audio) {
                var seconds = audio.duration;
                return this.convertTime(seconds);
            } else {
                return '00:00';
            }
        },
        //Display the audio time elapsed so far
        elapsedTime() {
            var audio = this.$refs.player;
            if (audio) {
                var seconds = audio.currentTime;
                return this.convertTime(seconds);
            } else {
                return '00:00';
            }
        },
        //Playback listener function runs every 100ms while audio is playing
        playbackListener() {
            var audio = this.$refs.player;
            //Sync local 'playbackTime' var to audio.currentTime and update global state
            this.playbackTime = audio.currentTime;
            
            //console.log("update: " + audio.currentTime);
            //Add listeners for audio pause and audio end events
            audio.addEventListener("ended", this.endListener);
            audio.addEventListener("pause", this.pauseListener);
        },
        //Function to run when audio is paused by user
        pauseListener() {
            this.isPlaying = false;
            this.listenerActive = false;
            this.cleanupListeners();
        },
        //Function to run when audio play reaches the end of file
        endListener() {
            this.isPlaying = false;
            this.listenerActive = false;
            this.cleanupListeners();
        },
        //Remove listeners after audio play stops
        cleanupListeners() {
            var audio = this.$refs.player;
            audio.removeEventListener("timeupdate", this.playbackListener);
            audio.removeEventListener("ended", this.endListener);
            audio.removeEventListener("pause", this.pauseListener);
            //console.log("All cleaned up!");
        },
        toggleAudio() {
            this.$emit("pauseall");
            var audio = this.$refs.player;
            //var audio = document.getElementById("audio-player");
            if (audio.paused) {
                audio.play();
                this.isPlaying = true;
            } else {
                audio.pause();
                this.isPlaying = false;
            }
        },
    },
    mounted: function() {
      // nextTick code will run only after the entire view has been rendered
      this.$nextTick(function() {
        
        var audio=this.$refs.player;
        //Wait for audio to load, then run initSlider() to get audio duration and set the max value of our slider 
        // "loademetadata" Event https://www.w3schools.com/tags/av_event_loadedmetadata.asp
        audio.addEventListener(
          "loadedmetadata",
          function() {
            this.initSlider();
          }.bind(this)
        );
        // "canplay" HTML Event lets us know audio is ready for play https://www.w3schools.com/tags/av_event_canplay.asp
        audio.addEventListener(
          "canplay",
          function() {
            this.audioLoaded=true;
          }.bind(this)
        );
        //Wait for audio to begin play, then start playback listener function
        this.$watch("isPlaying",function() {
          if(this.isPlaying) {
            var audio=this.$refs.player;
            this.initSlider();
            //console.log("Audio playback started.");
            //prevent starting multiple listeners at the same time
            if(!this.listenerActive) {
              this.listenerActive=true;
              //for a more consistent timeupdate, include freqtimeupdate.js and replace both instances of 'timeupdate' with 'freqtimeupdate'
              audio.addEventListener("timeupdate",this.playbackListener);
            }
          }
        });
        //Update current audio position when user drags progress slider
        this.$watch("playbackTime",function() {
        var diff=Math.abs(this.playbackTime-this.$refs.player.currentTime);
        
          //Throttle synchronization to prevent infinite loop between playback listener and this watcher
          if(diff>0.01) {
            this.$refs.player.currentTime=this.playbackTime;
          }
        });
      });
    },
    watch:{
        playbackTime:{
           deep: true,
           handler() {
                this.rangeBg = this.playbackTime / this.audioDuration * 100; 
           }
        },
        isPlaying:{
           deep: true,
           handler() {

           }
        }
    }
};
</script>

<style>

#audio-player-root
{
    margin-bottom:15px;
    width:300px;
    max-width:100%;
    direction: ltr;
}
/* Play/Pause Button */
.play-button{
    height: 45px
}

.times
{
    margin-top:5px;
    text-align: right;
}

.clearfix:after
{
    height:1px;
}

#button-div
{
    float:left;
    width:50px;
    text-align: left;
    color:#343a40
}

#button-div svg 
{
    color:#999999;
    cursor:pointer
}

#progress-bar
{
    float:left;
    width:calc(100% - 50px)!important;
    margin-top:15px;
    overflow:hidden
}

.spinner
{
    position:absolute;
    right:15px;
    top:53px;
    font-size:23px;
}

input[type="range"] {
    margin: auto;
    -webkit-appearance: none;
    position: relative;
    overflow: hidden;
    width: 100%;
    display:block;
    cursor: pointer;
    height:15px;
    outline: none;
    border-radius: 0; /* iOS */
    background: transparent;
}
input[type="range"]:focus {
    outline: none;
}


.rangeStyle
{
    position:relative;
    z-index: 1;
}

.rangeStyle.loaded:before
{
    content:"";
    top:0;
    right:0;
    width:100%;
    z-index:10;
    position: absolute;
    height:100%;
}

.rangeStyle .rangeBg
{
    position:absolute;
    top:5px;
    left:0;
    height:5px;
    width:50%;
    pointer-events: none;
    background-color:#30b6f6;
    z-index: 1;
    
}


input[type='range'] {
    width: 100%;
    -webkit-appearance: none;
    padding: 5px 0;
    direction: ltr;
    background-color:#e6e6e6;
    background-clip: content-box;
}

input[type='range']::-webkit-slider-runnable-track {
    height: 10px;
    -webkit-appearance: none;
    color: #13bba4;
    margin-top: -1px;
}

input[type='range']::-moz-range-runnable-track {
    height: 10px;
    -webkit-appearance: none;
    color: #13bba4;
    margin-top: -1px;
}

/* All the same stuff for IE */
input[type=range]::-ms-thumb {
    width: 15px;
    -webkit-appearance: none;
    height: 15px;
    border-radius: 50%;
    border:none;
    margin-top:-2px;
    z-index: 2;
    background-color:#30b6f6;
    position:relative;
    outline:none;
}

input[type='range']::-webkit-slider-thumb {
    width: 15px;
    -webkit-appearance: none;
    height: 15px;
    border-radius: 50%;
    border:none;
    margin-top:-2px;
    z-index: 2;
    background-color:#30b6f6;
    position:relative;
    outline:none;
}

input[type='range']::-moz-range-thumb {
    width: 15px;
    -webkit-appearance: none;
    height: 15px;
    border-radius: 50%;
    border:none;
    margin-top:-2px;
    z-index: 2;
    background-color:#30b6f6;
    position:relative;
    outline:none;
}

.right .rangeStyle .rangeBg
{
    background-color:#889a7b
}

.right  input[type='range']
{
    background-color: #c6dfb2;
}

.right  input[type='range']::-webkit-slider-thumb
{
    background: #889a7b;
}

.right  input[type='range']::-moz-range-thumb
{
    background: #889a7b;
}

/* All the same stuff for IE */
.right input[type=range]::-ms-thumb {
  background: #889a7b;
}

.right  #button-div svg 
{
    color:#889a7b;
}


</style>