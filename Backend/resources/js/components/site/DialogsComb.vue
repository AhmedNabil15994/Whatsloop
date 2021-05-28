<template>
        <a @click="getId(chat.id)">
             <div class='labels d-flex flex-column'>
                <span v-for="(label,labelKey) in chat.labels" :key="labelKey" :class="'ContactLabel '+ label.labelClass"></span>
            </div>
            <div class="media" :class="{'unread' : chat.unreadCount > 0}">

                <div class="chat-user-img online align-self-center ml-3">
                    <img onerror="this.onerror=null;this.src='https://whatsloop.net/resources/Gallery/UserDefault.png';" 
                    :src="chat.image ? chat.image : 'https://whatsloop.net/resources/Gallery/UserDefault.png'"
                    class="rounded-circle avatar-xs" alt="test" />
                </div>

                <div class="media-body overflow-hidden">
                    <h5 class="text-truncate DialogTitle font-size-16">
                        <h5 class="text-truncate DialogTitle font-size-16">
                            <span>{{chat.name}}</span>
                        </h5>
                    </h5>
                    <div :dir="isUnicode(chat.lastMessage.body) ? 'rtl' : 'ltr'" class="chat-user-message text-truncate mb-0" :class="{'msgRemoved' : !chat.lastMessage.body,'text-bold' : chat.lastMessage.unRead}">
                            
                            <span v-if="chat.lastMessage.whatsAppMessageType === 'chat' || chat.lastMessage.whatsAppMessageType === 'link'"> 
                            
                                <template v-if="chat.lastMessage.body.includes('*') || chat.lastMessage.body.includes('https://whatsloop.net/resources/Gallery/') && !chat.lastMessage.body.includes('iframe')">
                                    <span :inner-html.prop="chat.lastMessage.body | removeAst"></span>
                                </template>
                                <template v-else>{{ chat.lastMessage.body }}</template>
                                
                            </span>
                            
                        
                            <span v-else-if="chat.lastMessage.whatsAppMessageType === 'image' ||  chat.lastMessage.whatsAppMessageType === 'video'">
                                <template v-if="chat.lastMessage.caption">
                                    <i class="fa fa-image"></i> 
                                    <template v-if="chat.lastMessage.caption.includes('*') || chat.lastMessage.caption.includes('https://whatsloop.net/resources/Gallery/') && !chat.lastMessage.caption.includes('iframe')">
                                        <span :inner-html.prop="chat.lastMessage.caption | removeAst"></span>
                                    </template>
                                    <template v-else>
                                        {{ chat.lastMessage.caption }}
                                    </template>
                                </template>
                                <template v-else>
                                    <i class="fa fa-image"></i>
                                    <span v-if="chat.lastMessage.whatsAppMessageType === 'image'"> صور</span>
                                    <span v-else-if="chat.lastMessage.whatsAppMessageType === 'video'"> فيديو</span>
                                </template>

                            </span>

                            <span v-else-if="chat.lastMessage.whatsAppMessageType === 'document'"> 
                                    <span>تم مشاركة ملف <i class="fa fa-file"></i></span>
                            </span>

                            <span v-else-if="chat.lastMessage.whatsAppMessageType === 'location'"> 
                                    <span>تم مشاركة موقع <i class="fa fa-map-marker"></i></span>
                            </span>

                            <span v-else-if="chat.lastMessage.whatsAppMessageType === 'contact'"> 
                                    <span>تم مشاركة جهة اتصال <i class="fa fa-contact"></i></span>
                            </span>

                            <span v-else-if="chat.lastMessage.whatsAppMessageType === 'ptt'"> 
                                    <span>تم مشاركة مقطع صوتي <i class="fa fa-microphone"></i></span>                                                                           
                            </span>

                            <span v-else-if="chat.lastMessage.whatsAppMessageType === 'call_log'"> 
                                <template v-if="chat.lastMessage.fromMe == 1">
                                    <span>مكالمة صادرة <i style="color:#0f0;margin-left:5px;float:right;margin-top:4px" class="fa fa-phone"></i></span>
                                </template>
                                <template v-else>
                                    <span>مكالمة واردة <i  style="color:#f00;margin-left:5px;float:right;margin-top:4px" class="fa fa-phone"></i></span>
                                </template>
                                
                            </span>
                        
                        

                            <span v-else><i class="fa fa-ban"></i> لقد حذفت هذه الرسالة</span>
                    </div>
                    <p class="chatStatus clearfix mb-0" style="margin-left:3px;float:right;margin-top:-4px">
                        <span class="seen" v-if="chat.lastMessage.fromMe === 1" >
                            <svg
                                v-if="chat.lastMessage.sending_status == 0"
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 16 15"
                                width="16"
                                height="15"
                            >
                                <path
                                fill="currentColor"
                                d="M9.75 7.713H8.244V5.359a.5.5 0 0 0-.5-.5H7.65a.5.5 0 0 0-.5.5v2.947a.5.5 0 0 0 .5.5h.094l.003-.001.003.002h2a.5.5 0 0 0 .5-.5v-.094a.5.5 0 0 0-.5-.5zm0-5.263h-3.5c-1.82 0-3.3 1.48-3.3 3.3v3.5c0 1.82 1.48 3.3 3.3 3.3h3.5c1.82 0 3.3-1.48 3.3-3.3v-3.5c0-1.82-1.48-3.3-3.3-3.3zm2 6.8a2 2 0 0 1-2 2h-3.5a2 2 0 0 1-2-2v-3.5a2 2 0 0 1 2-2h3.5a2 2 0 0 1 2 2v3.5z"
                                ></path>
                            </svg>
                            <svg
                                v-if="chat.lastMessage.sending_status == 1"
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 16 15"
                                width="16"
                                height="15"
                            >
                                <path
                                fill="currentColor"
                                d="M10.91 3.316l-.478-.372a.365.365 0 0 0-.51.063L4.566 9.879a.32.32 0 0 1-.484.033L1.891 7.769a.366.366 0 0 0-.515.006l-.423.433a.364.364 0 0 0 .006.514l3.258 3.185c.143.14.361.125.484-.033l6.272-8.048a.365.365 0 0 0-.063-.51z"
                                ></path>
                            </svg>
                            <svg
                                v-if="chat.lastMessage.sending_status == 2"
                                style="color:#00000073"
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 16 15"
                                width="16"
                                height="15"
                            >
                                <path
                                fill="currentColor"
                                d="M15.01 3.316l-.478-.372a.365.365 0 0 0-.51.063L8.666 9.879a.32.32 0 0 1-.484.033l-.358-.325a.319.319 0 0 0-.484.032l-.378.483a.418.418 0 0 0 .036.541l1.32 1.266c.143.14.361.125.484-.033l6.272-8.048a.366.366 0 0 0-.064-.512zm-4.1 0l-.478-.372a.365.365 0 0 0-.51.063L4.566 9.879a.32.32 0 0 1-.484.033L1.891 7.769a.366.366 0 0 0-.515.006l-.423.433a.364.364 0 0 0 .006.514l3.258 3.185c.143.14.361.125.484-.033l6.272-8.048a.365.365 0 0 0-.063-.51z"
                                ></path>
                            </svg>
                            <svg
                                v-if="chat.lastMessage.sending_status == 3"
                                style="color:#4fc3f7"
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 16 15"
                                width="16"
                                height="15"
                            >
                                <path
                                fill="currentColor"
                                d="M15.01 3.316l-.478-.372a.365.365 0 0 0-.51.063L8.666 9.879a.32.32 0 0 1-.484.033l-.358-.325a.319.319 0 0 0-.484.032l-.378.483a.418.418 0 0 0 .036.541l1.32 1.266c.143.14.361.125.484-.033l6.272-8.048a.366.366 0 0 0-.064-.512zm-4.1 0l-.478-.372a.365.365 0 0 0-.51.063L4.566 9.879a.32.32 0 0 1-.484.033L1.891 7.769a.366.366 0 0 0-.515.006l-.423.433a.364.364 0 0 0 .006.514l3.258 3.185c.143.14.361.125.484-.033l6.272-8.048a.365.365 0 0 0-.063-.51z"
                                ></path>
                            </svg>
                        </span>
                    </p>
                </div>
                
                <div class="circles">
                    <span class=" pinTop" v-if="chat.is_pinned == 1">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 19 19" width="19" height="19"><path fill="currentColor" d="M9.5 18.419C4.574 18.419.581 14.426.581 9.5S4.574.581 9.5.581s8.919 3.993 8.919 8.919-3.993 8.919-8.919 8.919zm2.121-5.708l-.082-2.99 1.647-1.963a1.583 1.583 0 0 0-.188-2.232l-.32-.269a1.58 1.58 0 0 0-2.231.203L8.803 7.42l-2.964.439a.282.282 0 0 0-.14.496l5.458 4.58c.186.157.47.019.464-.224zM5.62 13.994a.504.504 0 0 0 .688-.038l2.204-2.307-1.085-.91-1.889 2.571a.504.504 0 0 0 .082.684z"></path></svg>
                    </span>
                    <div class="unread-message" v-if="chat.unreadCount > 0">
                        <span class="">{{ chat.unreadCount }}</span>
                    </div>
                    
                    <span class="fa fa-user admins"  :id="'admins'+chat.id" v-if="chat.moderators.length > 0"></span>
                    <b-tooltip v-if="chat.moderators.length > 0" :target="'admins'+chat.id">
                        <div v-for="(Moderators,indexMChat) in chat.moderators" :key="indexMChat">
                            {{ Moderators.name }} <br>
                        </div>
                    </b-tooltip>
                </div>
                
                <div class="font-size-11">{{ chat.last_time }}</div>
            </div>

        </a>
</template>
<script>
export default {
    props:['chat'],
    name:"dialogscomb",
    data() {
        return {
            token:null
        }
    },
    mounted () {
        
    },
    methods: {
        isUnicode(str) {
            var reg = /[.!@#$%^&*()>+-<,{}[/|\]]/;
            if( reg.test(str.charAt(0)))
            {
                var letters = [];
                for (var i = 0; i <= str.length; i++) {
                    
                    letters[i] = str.substring(i - 1, i);
                    if(letters[i].charCodeAt() === 160) {
                        return false;
                    } else {
                        if (letters[i].charCodeAt() > 255 ) {
                            return true;
                        }
                    }
                }
                return false;

            }
            else {
                if(str.charAt(0).charCodeAt() < 255) {
                    return false;
                } else {
                    return true;
                }
            }
        },
        getId(id) {
            this.$store.dispatch("chatIdAction", {id:id});
            
            if(this.chat.unreadCount >  0) {
                this.chat.unreadCount = 0;
                var data = new FormData();
                data.append('chatId',id);
                this.$http.post(this.$store.getters.urlApi+`readChat`,data);
                
            }
        }
    }
}
</script>
<style>
    
</style>