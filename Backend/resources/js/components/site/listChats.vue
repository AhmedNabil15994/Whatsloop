<template>
    <div>
        <div class="chat-leftsidebar" style="position:relative">

            <div class="tab-content">

                <!-- Start chats tab-pane -->
                <div class="tab-pane fade show active" id="pills-chat" role="tabpanel" aria-labelledby="pills-chat-tab">
                    <!-- Start chats content -->
                    <div>
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h4 class="titleChats">المحادثات </h4>
                            </div>
                            <div class="col-4 text-left">
                                <i class="openNew" @click="openNew = !openNew" v-b-tooltip.hover title="محادثة جديدة">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"><path fill="currentColor" d="M19.005 3.175H4.674C3.642 3.175 3 3.789 3 4.821V21.02l3.544-3.514h12.461c1.033 0 2.064-1.06 2.064-2.093V4.821c-.001-1.032-1.032-1.646-2.064-1.646zm-4.989 9.869H7.041V11.1h6.975v1.944zm3-4H7.041V7.1h9.975v1.944z"></path></svg>
                                </i>
                            </div>
                            
                        </div>
                         <!-- .p-4 -->
                        <transition name="fade">
                            <form v-if="openNew === false" @submit.prevent class="searchDiv">
                                <div class="search-box chat-search-box">
                                    <div class="input-group bg-light  input-group-lg rounded-lg">
                                        <div class="input-group-prepend">
                                            <button @click="searchText()" class="btn btn-link text-muted" type="button">
                                                <i class="ri-search-line search-icon font-size-14"></i>
                                            </button>
                                        </div>
                                        <input type="text" class="form-control bg-light" :value="searchTo" @input="updateValue($event.target.value)"  placeholder="ابحث بالاسم او رقم الجوال">
                                    </div> 
                                </div> <!-- Search Box-->
                            </form>
                        </transition>
                        
                        
                        <vuescroll>
                        <!-- Start chat-message-list -->
                            <div class="paddingLeft">
                            <!--  -->
                                <div class="chat-message-list"  data-simplebar>
                                        <center v-if="loading === true">
                                            <div class="lds-roller"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
                                        </center>
                                        <div>
                                            
                                            <ul v-if="!searchTo" class="list-unstyled chat-list  chat-user-list">
                                                <li v-for="(chatPin,indexPin) in chatsPin" 
                                                :class="chatId == chatPin.id ?  'active' : ''"
                                                :key="indexPin" @click="openCht()">
                                                    <div v-if="chatsPin.length > 0">
                                                    
                                                        <dialogscomb :chat="chatPin" v-if="chatPin.is_pinned == 1"></dialogscomb>
                                                    </div>


                                                </li>
                                            </ul>

                                            <ul v-if="!searchTo" class="list-unstyled chat-list  chat-user-list">
                                                <li v-for="(NewMsg,indexNew) in newMsg" 
                                                :class="chatId == NewMsg.id ?  'active' : ''"
                                                :key="indexNew" @click="openCht()">
                                                    <template v-if="newMsg">
                                                    
                                                        <dialogscomb :chat="NewMsg" v-if="NewMsg.is_pinned == 0"></dialogscomb>
                                                
                                                    </template>
                                                </li>
                                            </ul>
                                            <ul v-if="!searchTo" class="list-unstyled chat-list  chat-user-list">
                                                <li v-for="(chat,indexC) in chats.Dialogs" 
                                                :class="chatId == chat.id ?  'active' : ''"
                                                :key="indexC" @click="openCht()">
                                                    <div v-if="chat.last_time !== '1970-01-01'">
                                                    
                                                        <a v-if="!chat.lastMessage">
                                                            <div class="media">
                                        
                                                                <div class="chat-user-img online align-self-center ml-3">
                                                                    <img 
                                                                    src="https://whatsloop.net/resources/Gallery/UserDefault.png"
                                                                    class="rounded-circle avatar-xs" alt="test" />
                                                                </div>
                                        
                                                                <div class="media-body overflow-hidden">
                                                                    <h5 class="text-truncate DialogTitle font-size-16">
                                                                        <h5 class="text-truncate DialogTitle font-size-16">
                                                                            <span>{{chat.name}}</span>
                                                                        </h5>
                                                                    </h5>
                                                                    <div>
                                                                        <span>رسالة محذوفة او غير مدعمة <i class="fa fa-ban"></i></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </a>
                                                        <div v-else>
                                                            <dialogscomb :chat="chat" v-if="chat.is_pinned == 0"></dialogscomb>
                                                        </div>
                                                    </div>
                                                </li>
                                                <scroll-loader v-if="totalCount === false && chats.Dialogs !== null && openMainMsgs === false"  :loader-method="loadMore"></scroll-loader>
                                            </ul>
                                                
                                            <ul v-if="searchTo" class="list-unstyled chat-list  chat-user-list">
                                                <li v-for="(chatSrch,indexSearch) in searchDiv" 
                                                :class="chatId == chatSrch.id ?  'active' : ''"
                                                :key="indexSearch" @click="openCht()">
                                                    <div v-if="chatSrch.last_time !== '1970-01-01'">
                                                        <a v-if="!chatSrch.lastMessage">
                                                            <div class="media">
                                        
                                                                <div class="chat-user-img online align-self-center ml-3">
                                                                    <img 
                                                                    src="https://whatsloop.net/resources/Gallery/UserDefault.png"
                                                                    class="rounded-circle avatar-xs" alt="test" />
                                                                </div>
                                        
                                                                <div class="media-body overflow-hidden">
                                                                    <h5 class="text-truncate DialogTitle font-size-16">
                                                                        <h5 class="text-truncate DialogTitle font-size-16">
                                                                            <span>{{chatSrch.name}}</span>
                                                                        </h5>
                                                                    </h5>
                                                                    <div>
                                                                        <span>رسالة محذوفة او غير مدعمة <i class="fa fa-ban"></i></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </a>
                                                    </div>

                                                    <div v-else>
                                                        <dialogscomb :chat="chatSrch"></dialogscomb>
                                                    </div>
                                                </li>
                                            </ul>
                                            <div class="errSearch" v-if="searchTo !== '' && searchDiv.length === 0 && loading === false">عفوا لا يوجد نتائج بحث</div>
                                            <!--
                                            <ul v-if="!searchTo && openMainMsgs === true" class="list-unstyled chat-list  chat-user-list">
                                                <li v-for="(chatMain,indexM) in chats.Dialogs" 
                                                :class="chatId == chatMain.id ?  'active' : ''"
                                                :key="indexM" @click="openCht()">
                                                    <a v-if="!chatMain.lastMessage">
                                                        <div class="media">
                                    
                                                            <div class="chat-user-img online align-self-center ml-3">
                                                                <img 
                                                                src="https://whatsloop.net/resources/Gallery/UserDefault.png"
                                                                class="rounded-circle avatar-xs" alt="test" />
                                                            </div>
                                    
                                                            <div class="media-body overflow-hidden">
                                                                <h5 class="text-truncate DialogTitle font-size-16">
                                                                    <h5 class="text-truncate DialogTitle font-size-16">
                                                                        <span>{{chatMain.name}}</span>
                                                                    </h5>
                                                                </h5>
                                                                <div>
                                                                    <span>رسالة محذوفة او غير مدعمة <i class="fa fa-ban"></i></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </a>
                                                    <div v-else>
                                                        <dialogscomb :chat="chatMain" v-if="chatMain.is_pinned == 0"></dialogscomb>
                                                    </div>
                                                </li>                                     
                                            </ul>-->

                                        </div>
                                        <!--
                                        <div class="chat-message-list notFound" v-if="chats.Dialogs.length === 0 || chats.Dialogs === undefined">
                                             لا يوجد محادثات
                                        </div>-->

                                </div>
        
                            </div>
                        <!-- End chat-message-list -->
                        </vuescroll>
                    </div>
                    <!-- Start chats content -->
                </div>
                <!-- End chats tab-pane -->
                
            </div>
            <!-- end tab content -->
            <transition name="slideRight">
                <newmsg v-on:closeNew="openNew = false" v-if="openNew"></newmsg>
            </transition>

        </div>

    </div>
</template>
<script>
import vuescroll from 'vuescroll';
import dialogscomb from './DialogsComb';
import newmsg from './newMsg';


export default {
    props:["totalCount","openMainMsgs","chats","chatsPin","AllCounts",
    "loading","searchDiv","searchTo",
    "loadMor","distance","DialogID",
    "newMsg","UserData","searchMethod","InstanceNumber"],
    name:"listChats",
    data()
         {
        return {
            openNew:false
        }
    },
    watch:{
        currentScrollY(val) {
            if (val > 0) {
                this.headerFixed = true;
            } else {
                this.headerFixed = false;
            }
        }
    },
    methods: {
        loadMore() {
            this.$emit("loadMor")
        },
        openCht() {
            this.$emit("openCht");
        },
        searchText() {
            this.$emit("searchMethod");
        },
        updateValue(val) {
            this.$emit("input",val);
        }

    },
    computed:{
        currentScrollY() {
        return this.$store.getters["currentScrollY"];
        },
        chatId() {
            return this.$store.getters["chatId"];
        }
    },
    components:{
        vuescroll,
        dialogscomb,
        newmsg
    }
}
</script>

<style>

.SingleOrMul
{
    margin-bottom:20px;
}

.SingleOrMul li 
{
    padding:0 15px;
    height:30px;
    line-height:30px;
    color:#7a7f9a;
    cursor:pointer;
    font-family: "Tajawal-Bold";
    border-radius: 5px;
    -webkit-transition:all 0.3s;
    -moz-transition:all 0.3s;
    -o-transition:all 0.3s;
    transition:all 0.3s;
}

.SingleOrMul li:hover,
.SingleOrMul li.active
{
    color:#fff;
    background-color:#00bfa5
}

.SingleOrMul li:first-of-type
{
    float:right;
}

.SingleOrMul li:last-of-type
{
    float:left;
}

.textPlaceHolder
{
    margin-bottom: 15px;
    font-size: 14px;
    color: #e5e5e5;
    position: absolute;
    width: calc(100% - 60px);
    right: 15px;
    top: 15px;
    color: #848688;
    z-index: -1;
    direction: rtl;
}

.inputTags
{
  position:relative;
  direction: ltr;
  text-align: right;
  padding:10px 15px;
  min-height:100px;
  border:none;
  border-radius: 5px;
  margin-bottom:20px;
  cursor:text;
  z-index:1;
  max-height:150px;
  overflow:auto;
  background-color:#f3f6f9;
  display:flex;
  flex-wrap: wrap;
}

.inputTags input
{
    background:none;
    border:none;
    height:32px;
    -webkit-box-flex: 1;
    -ms-flex-positive: 1;
    flex-grow: 1;
}

.inputTags .listNumbs
{
  color:#fff;
  background-color:#00bfa5;
  border-color:#00bfa5;
  border-radius: 5px;
  padding:5px 20px 3px 10px;
  margin-bottom:2px;
  margin-top:2px;
  margin-right:2.5px;
  margin-left:2.5px;
  height:30px;
  max-width:100%;
  position:relative;
  float:left;
}

.inputTags .listNumbs .fa-close
{
    float:right;
    padding:0 5px;
    cursor:pointer;
    position: absolute;
    right:0;
    top:0;
    height:30px;
    line-height:30px;
}

.inputTags input
{
    float:left;
}

.alertNumbs .close
{
    position:absolute;
    left:10px;
    top:10px;
    font-size:14px;
    opacity:1;
    color:#000;
    cursor:pointer;
}

.alertNumbs ul
{
    margin-top:5px;
}

.newMsg
{
    position: absolute;
    right:0;
    top:0;
    width:100%;
    height:100%;
    z-index: 100;
    overflow:auto;
    background-color:#fff;
    transition:all 0.3s;
}

.newMsg.active
{
    right:0;
}

.newMsg .head
{
    height:60px;
    line-height:60px;
    padding:0 20px;
    background-color:#00bfa5;
    color:#fff
}

.newMsg .head .title
{
    float:right;
    color:#fff;
    font-family: "Tajawal-Medium";
    font-weight:normal;
    height:60px;
    line-height:60px;
}

.newMsg .head .back
{
    float:left;
    color:#fff;
    height:60px;
    line-height:60px;
    cursor:pointer
}

.newMsg .relativeInput
{
    position:relative
}

.newMsg .body
{
    padding:20px;
}

.newMsg .body .titleInput
{
    color: #7a7f9a;
    margin-bottom:10px;
    display:block
}

.newMsg .body .send
{
    border-radius: 35px;
    border:none;
    font-family: "Tajawal-Bold";
    color:#fff;
    height:40px;
    width:100%;
    background-color:#00bfa5;
    max-width:250px;
    display:block;
    font-size:17px;
    margin:35px auto 0
}

@media (max-width:991px) {
    .newMsg .body .send
    {
        line-height:42px;
        margin-top:20px;
    }
}

.newMsg .body .send:disabled {
  background: #919191;
}


.chat-message-list {
    height: calc(100vh - 113px)!important;
}

@media(max-width:991px) {
    .chat-message-list {
        height: calc(100vh - 180px)!important;
        padding-top:5px;
    }
}

.nice-bar {
    height:100%
}

.labels
{
    height: 100%;
    position: absolute;
    top: 0px;
    z-index: 98;
    right:5px;
}

.ContactLabel
{
    width: 3px;
    height: 100%;
    display: inline-block;
    margin-bottom: 2px;
}

.circles
{
    position: absolute;
    left: 16px;
    bottom: 23px;
    z-index: 99;
}


.circles .totalNew
{
    width: 25px;
    height: 25px;
    text-align: center;
    color: #fff;
    font-family: "Tajawal-Bold";
    font-size: 14px;
    background-color: #00C5BB;
    border-radius: 50%;
    line-height: 25px;
    float:left;
    margin-right:5px;
}

.circles .pinTop,
.circles .admins
{
    width: 19px;
    height: 19px;
    text-align: center;
    color: #fff;
    font-size: 11px;
    background-color: #00C5BB;
    border-radius: 50%;
    line-height: 19px;
    float:left;
    margin-right:5px;
    background-color:#FFA800
}

.circles .pinTop
{
    margin-top: -1px;
    background:none;
    color:#c2c2c2
}

.unread-message
{
    float: left;
    padding: .3em .4em .2em;
    border-radius: 1.1em;
    min-width: 1.49em;
    min-height: 1em;
    font-weight: 600;
    font-size: 12px;
    line-height: 1em;
    vertical-align: top;
    background-color: #06d755;
    color: #fff;
    text-align: center;
    margin-right:5px;
}

.chat-list li .unread-message
{
    position: static!important;
}

.circles .admins
{
    background-color:#3699ff
}

.chat-list li.active a,
.chat-list li.active a:hover
{
    background-color:#ebebeb!important
}


.chat-list li a:hover
{
    background-color:#f5f5f5!important
}

.chat-list li a
{
    border-color:#f5f5f5;
    border-radius: 0;
    padding:14px 20px 13px!important
}

.chat-list li .chat-user-message
{
    width: 85%;
    margin-top:-3px;
    display:inline-block
}

.chat-list li .chat-user-message .fa-ban,
.chat-list li .chat-user-message .fa-map-marker
{
    margin-top:4.5px;
    margin-left:3px;
    float:right;
    font-size:13px;
}

.chat-list li .chat-user-message .fa-image
{
    margin-left: 5px;
    float: right;
    margin-top: 3px;
}

.DialogTitle {
    direction: ltr;
    margin-bottom:-3px;
}

.imgErr
{
    text-align: center;
    line-height:2.2rem;
    background-color:rgba(27,197,189,.25);
    color:#1bc5bd
}

.chat-leftsidebar
{
    background-color:#fff!important;
    padding-left:0!important;
    border-left:1px solid #00000014
}

.bg-light {
    background-color: #f3f6f9!important;
}

.errSearch
{
    padding:30px 0;
    text-align: center;
    color:#A2A2A2;
    font-size:18px;
}

.paddingLeft
{
    padding-left:5px;
}

.searchDiv
{
    padding: 9px 15px;
    background-color: #f6f6f6;
    box-shadow: 0 1px 3px #00000026;
    z-index: 1;
    position: relative;
}

.searchDiv .bg-light
{
    background-color:#fff!important;
    border-radius: 42px!important;
    height: 35px!important;
}

.searchDiv .bg-light button
{
    outline:none;
    box-shadow: none;
    border:none;
    text-decoration: none;
    padding-left:10px;
}

.searchDiv .bg-light input
{
    padding-right:5px;
    direction:ltr;
    text-align:right
}

.avatar-xs
{
    height: 3.1rem!important;
    width: 3.1rem!important;
}

.media-body
{
    margin-top:4px
}

@media (max-width:480px) {
    .circles
    {
        bottom:23px;
    }
}

.openNew
{
    height: 60px;
    line-height: 60px;
    padding: 0 20px;
    color:#919191;
    cursor:pointer;
    transform: scaleX(-1);
}

.svgStore
{
    float:right;
    width:16px;
    height:14px;
    float:right;
    margin-top:4px;
    margin-left:5px;
}

.svgStore path{
    fill:rgba(0, 0, 0, 0.45)
}

.notFound
{
    padding:40px 20px;
    text-align:center;
    color:#495057
}


</style>