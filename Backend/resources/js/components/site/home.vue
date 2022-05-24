<template>
    <div  class="layout-wrapper d-lg-flex clearfix" :class="subscription === false ? 'paddingTop' : ''">

        <transition name="fade" >
            <div class="alertSub" v-if="subscription === false">
                <i class="fa fa-exclamation-triangle"></i>
                {{this.errorMsg !== null ? this.errorMsg : 'يرجي التواصل مع الدعم الفني'}}   
            </div>
        </transition>

        <!-- Start left sidebar-menu -->
        <div class="side-menu flex-lg-column">
            <!-- LOGO -->
            <div class="navbar-brand-box">
                
                <div  class="logo" v-if="UserData === null">
                    <span class="logo-sm">
                        <img src="/images/logo.svg" alt="" height="30">
                    </span>
                </div>
            </div>
            <!-- end navbar-brand-box -->

            <!-- Start side-menu nav -->
            <div class="flex-lg-column my-auto" id="navDiv">
                <ul class="nav nav-pills side-menu-nav justify-content-center" role="tablist">
                    <li class="nav-item" v-if="reference === true">
                        <a @click="getChatsClick();activeList();"  class="nav-link active" v-b-tooltip.hover  title="المحادثات">
                            <i class="ri-message-3-line"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                            <a @click="openMain();activeList();"  class="nav-link" :class="reference == false ?  'active' : ''" v-b-tooltip.hover title="محادثاتي">
                            <i class="ri-pie-chart-line"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" @click="logOut();activeList();" v-b-tooltip.hover title="خروج" href="#">
                            <i class="ri-user-settings-line"></i>
                        </a>
                    </li>
                </ul>
            </div>
            <!-- end side-menu nav -->

        </div>
        <!-- end left sidebar-menu -->
        <!-- v-if="getContact !== '' && chatId !== 0" -->
        <!-- start chat-leftsidebar -->
            <list-chats class="transition" :class="openChat === true ? 'hidMob' : ''" v-on:openCht="openCht2()"
            :openMainMsgs="openMainMsgs"
            :chats="chats"
            :chatsPin="chatsPin"
            :AllCounts="AllCounts"
            :loading="loading"
            :totalCount="totalCount"
            v-model="searchTo"
            :searchTo="searchTo"
            :searchDiv="searchDiv"
            v-on:loadMor="loadMore()"
            v-on:totalCountTrue="totalCountTrue()"
            v-on:searchMethod="search()"
            :distance="distance"
            :DialogID="DialogID"
            :newMsg="newMsg"
            :UserData="UserData"
            :InstanceNumber="InstanceNumber"
            ></list-chats>
        <div class="startChat w-100 flex-lg-column overflow-hidden " v-if="chatId === 0">
            <img v-if="chatId === 0" src="/images/logo.svg"/>
        </div>
        <!-- end chat-leftsidebar -->
        <transition name="slide">
        <chat-component
            v-on:openCht="openCht()" 
            v-on:opencontct="opencontct()"
            v-on:checkSubscription="checkSubscription"
            v-on:childToParent="removeMsgParent"
            :contact="getContact" 
            v-if="getContact !== [] && chatId !== 0"
            :openChat="openChat" 
            :chatId="chatId"></chat-component>
        </transition>
        <!-- end chat-leftsidebar -->
        <contact 
            v-on:changeModeratorsC="changeModsMethod"
            v-on:opencontct="opencontct()" 
            :options="options"
            :supervisors="supervisors"
            v-if="getContact.length !== 0 && openContact == true && chatId !== 0" 
            :openContact="openContact" 
            :contact="getContact"></contact>
    </div>
</template>
<script>
import ListChats from './listChats.vue';
import chatComponent from './chatComponent.vue';
import Contact from './contact';


export default {
    name:"home",
    data() {
        return {
            chats:{
                Dialogs:null
            },
            chatsPin:[],
            AllCounts:null,
            loading:true,
            searchTo:"",
            searchDiv:[],
            load:1,
            distance:500,
            DialogID:[],
            DialogsBin:[],
            newMsg:[],
            UserData:null,
            InstanceNumber:null,
            openChat:false,
            openContact:false,
            openMainMsgs:false,
            totalCount:false,
            options:[],
            supervisors:[],
            moderatorsContact:[],
            reference:false,
            lastPage:0,
            subscription:true,
            errorMsg:null
        }
    },
    created() {

    },
    mounted () {
    
        this.activeList();

        this.getChats(this.load);

     
        var domain =  window.location.host.split('.')[1] ? window.location.host.split('.')[0] : false;
        
      this.testBroadCastingSentMessage(domain);
      this.testBroadCastingIncomingMessage(domain);
      this.testBroadCastingBotMessage(domain);
      this.testBroadUpdateDialogPinStatus(domain);
      this.testBroadUpdateMessageStatus(domain);
      this.testBroadUpdateChatReadStatus(domain);
      this.testBroadUpdateChatLabelStatus(domain);

      this.$http.get(this.urlApi+`labels`)
      .then((res) => {
              this.options = res.data.data;
      });

      this.$http.get(this.urlApi+`moderators`)
      .then((res) => {
              this.supervisors = res.data.data;
      });



    },
    watch:{
        newMsg:{
            handler() {
                for (var i in this.chats.Dialogs) {
                    if(this.DialogID.includes(this.chats.Dialogs[i].id)) {
                        this.chats.Dialogs.splice(i, 1);
                    }  
                }  
                for (var c in this.chatsPin) {
                    if(this.DialogID.includes(this.chats.Dialogs[c].id)) {
                        this.chatsPin.splice(c, 1);
                    }  
                }  

            },
            deep: true
        },
        searchTo:{
            handler() {
                this.search();
            },
            deep:true
        },
        moderatorsContact:{
            handler() {
                this.changeMods();
            },
            deep:true
        },
        getContact:{
            handler() {
                this.changeName();
                if(this.getContact.labelsColors && this.getContact.labelsText) {
                    this.changeLabels();
                }
                
            },
            deep:true
        }
    },
    methods: {
        removeMsgParent(id) { 
            for(var msg in this.chats.Dialogs) {
                if(this.chats.Dialogs[msg].lastMessage.id === id) 
                {
                    this.chats.Dialogs[msg].lastMessage.deleted_by = 'me';
                    this.chats.Dialogs[msg].lastMessage.whatsAppMessageType = 'deleted';
                    this.chats.Dialogs[msg].lastMessage.message_type = 'deleted';
                    break;
                }
            }
        },
        checkSubscription(err) {
            this.errorMsg = err;
            this.subscription = false;
            setTimeout(() => {
                this.subscription = true;
                this.errorMsg = null;
            },4000)
        },
        totalCountTrue() {
            this.totalCount = true;
        },
        testBroadCastingSentMessage (domain) {
        // Start socket.io listener
          window.Echo.channel(domain+'-NewSentMessage')
            .listen('SentMessage', (data) => {
              this.searchPucher(data.message);
            })
          // End socket.io listener
        },
        testBroadCastingIncomingMessage (domain) {
                
            // Start socket.io listener
            window.Echo.channel(domain+'-NewIncomingMessage')
            .listen('IncomingMessage', (data) => {
               // console.log(data)
                if(this.chatId !== data.message.id) {
                    var audio = new Audio('/swiftly.mp3'); // path to file
                    audio.play();
                }
                this.searchPucher(data.message);
            });
          // End socket.io listener
        },
        /*testBroadCastingBotMessage (domain) {
        // Start socket.io listener
        window.Echo.channel(domain+'-NewBotMessage')
            .listen('BotMessage', (data) => {
                    data.message.lastMessage.body = data.message.lastMessage.bot_details.message;
                    this.searchPucher(data.message);
                
            })
          // End socket.io listener
        },*/
        testBroadCastingBotMessage (domain) {
        // Start socket.io listener
          window.Echo.channel(domain+'-NewBotMessage')
            .listen('BotMessage', (data) => {
                    //data.message.lastMessage.body = data.message.lastMessage.bot_details.message;
                    // edited by nabil
                    if(data.message.lastMessage.bot_details.reply_type == 2){
                        if(data.message.lastMessage.whatsAppMessageType == 'image'){
                            data.message.lastMessage.caption = data.message.lastMessage.bot_details.message;
                            data.message.lastMessage.body = data.message.lastMessage.bot_details.file;
                        }else{
                            data.message.lastMessage.caption = data.message.lastMessage.bot_details.file_name;
                        }
                    }
                   // console.log(data)
                    // end nabil edit
                    this.searchPucher(data.message);
            })
          // End socket.io listener
        },
        testBroadUpdateDialogPinStatus (domain) {
           
            // Start socket.io listener
            window.Echo.channel(domain+'-UpdateDialogPinStatus')
            .listen('DialogPinStatus', (data) => {
                
                for (var remBin in this.DialogsBin) {
                    if(this.DialogsBin[remBin] === data.chatId.id) {
                        this.DialogsBin.splice(remBin,1);              
                    }
                }
                if(data.statusInt === 0 ) {
                    for (var cPin in this.chatsPin) {
                        if(data.chatId.id ===  this.chatsPin[cPin].id) {
                            this.chatsPin.splice(cPin, 1);
                        }
                    } 
                    for (var iChat1 in this.chats.Dialogs) {
                        if(data.chatId.id ===  this.chats.Dialogs[iChat1].id) {
                            this.chats.Dialogs[iChat1].is_pinned = 0;
                        }
                    }  

                    for (var iChat11 in this.newMsg) {
                        if(data.chatId.id ===  this.newMsg[iChat11].id) {
                            this.newMsg[iChat11].is_pinned = 0;
                        }
                    }  
                   for (var iChat33 in this.searchDiv) {
                        if(data.chatId.id ===  this.searchDiv[iChat33].id) {
                            this.searchDiv[iChat33].is_pinned = 0;
                        }
                    }  
                    
                    
                } 

                if(data.statusInt === 1) {
                    for (var cPin1 in this.chatsPin) {
                        if(data.chatId.id ===  this.chatsPin[cPin1].id) {
                            this.chatsPin.splice(cPin1, 1);
                        }
                    } 
                    for (var iChat in this.chats.Dialogs) {
                        if(data.chatId.id ===  this.chats.Dialogs[iChat].id) {
                            this.chats.Dialogs[iChat].is_pinned = 1;
                            if(!this.DialogsBin.includes(data.chatId.id)) {
                                this.chatsPin.push(data.chatId);
                                this.DialogsBin.push(data.chatId.id);
                            }
                        }
                    }  
                    for (var iChat2 in this.newMsg) {
                        if(data.chatId.id ===  this.newMsg[iChat2].id) {
                            //console.log(data.chatId.id  +' ' + this.newMsg[iChat2].id)
                            this.newMsg[iChat2].is_pinned = 1;
                            if(!this.DialogsBin.includes(data.chatId.id)) {
                                this.chatsPin.push(data.chatId);
                                this.DialogsBin.push(data.chatId.id);
                            }
                        }
                    }  
                    for (var iChat3 in this.searchDiv) {
                        if(data.chatId.id ===  this.searchDiv[iChat3].id) {
                            this.searchDiv[iChat3].is_pinned = 1;
                            if(!this.DialogsBin.includes(data.chatId.id)) {
                                this.chatsPin.push(data.chatId);
                                this.DialogsBin.push(data.chatId.id);
                            }
                        }
                    }  
                }
              
              
            })
             // End socket.io listener
        },
        testBroadUpdateMessageStatus (domain) {
            // Start socket.io listener
            window.Echo.channel(domain+'-UpdateMessageStatus')
                .listen('MessageStatus', (data) => {
                    this.chatsPin.forEach((element) => {
                        if (element.id) {
                            if (element.id.search(data.chatId) !== -1) {
                                element.lastMessage.sending_status = data.statusInt;
                            }
                        }
                    });
                    this.chats.Dialogs.forEach((element) => {
                        if (element.id) {
                            if (element.id.search(data.chatId) !== -1) {
                                element.lastMessage.sending_status = data.statusInt;
                            }
                        }
                    });
                    this.newMsg.forEach((element) => {
                        if (element.id) {
                            if (element.id.search(data.chatId) !== -1) {
                                element.lastMessage.sending_status = data.statusInt;
                            }
                        }
                    });
                    this.searchDiv.forEach((element) => {
                        if (element.id) {
                            if (element.id.search(data.chatId) !== -1) {
                                element.lastMessage.sending_status = data.statusInt;
                            }
                        }
                    });

                })
            // End socket.io listener
        },
        testBroadUpdateChatReadStatus (domain) {
        // Start socket.io listener
          window.Echo.channel(domain+'-UpdateChatReadStatus')
            .listen('ChatReadStatus', (data) => {
                    
                    this.chatsPin.forEach((element) => {
                        if (element.id.search(data.chatId.id) !== -1) {
                            element.is_read =  0;
                            element.unreadCount = 0;
                        }
                    });

                    this.chats.Dialogs.forEach((element) => {
                        if (element.id.search(data.chatId.id) !== -1) {
                            element.is_read =  0;
                            element.unreadCount = 0;
                        }
                    });

                    this.newMsg.forEach((element) => {
                        if (element.id.search(data.chatId.id) !== -1) {
                            element.is_read =  0;
                            element.unreadCount = 0;
                        }
                    });

                    this.searchDiv.forEach((element) => {
                        if (element.id.search(data.chatId.id) !== -1) {
                            element.is_read =  0;
                            element.unreadCount = 0;
                        }
                    });

                    /*for (var iChat in this.chats.chatsPin) {
                       if(data.chatId.id ===  this.chats.chatsPin[iChat].id) {
                            this.chats.chatsPin[iChat].is_read = 0;
                            this.chats.chatsPin[iChat].unreadCount = 0;
                        }
                    }  

                    for (var iChat1 in this.chats.Dialogs) {
                       if(data.chatId.id ===  this.chats.Dialogs[iChat1].id) {
                            this.chats.Dialogs[iChat1].is_read = 0;
                            this.chats.Dialogs[iChat1].unreadCount = 0;
                        }
                    }  
                    for (var iChat2 in this.newMsg) {
                       if(data.chatId.id ===  this.newMsg[iChat2].id) {
                            this.newMsg[iChat2].is_read = 0;
                            this.newMsg[iChat2].unreadCount = 0;
                        }
                    }  
                    for (var iChat3 in this.searchDiv) {
                       if(data.chatId.id ===  this.searchDiv[iChat3].id) {
                            this.searchDiv[iChat3].is_read = 0;
                            this.searchDiv[iChat3].unreadCount = 0;

                        }
                    }  */
            })
          // End socket.io listener
        },
        testBroadUpdateChatLabelStatus (domain) {
        // Start socket.io listener
          window.Echo.channel(domain+'-UpdateChatLabelStatus')
            .listen('ChatLabelStatus', (data) => {
                this.chatsPin.forEach((element) => {
                    if (element.id.search(data.chatId.id) !== -1) {
                        element.labels =  data.chatId.labels;
                    }
                });
                this.chats.Dialogs.forEach((element) => {
                    if (element.id.search(data.chatId.id) !== -1) {
                        element.labels =  data.chatId.labels;
                    }
                });
                this.newMsg.forEach((element) => {
                    if (element.id.search(data.chatId.id) !== -1) {
                        element.labels =  data.chatId.labels;
                    }
                });
                this.searchDiv.forEach((element) => {
                    if (element.id.search(data.chatId.id) !== -1) {
                        element.labels =  data.chatId.labels;
                    }
                });
            })
          // End socket.io listener
        },
        openCht() {
                this.openChat = false;
                this.$store.dispatch("chatIdAction", {id:0});
        },
        openCht2() 
        {
            this.openChat = true
        },
        activeList() {
            var btns = document.getElementsByClassName("nav-link");
            for (var i = 0; i < btns.length; i++) {
            btns[i].addEventListener("click", function() {
                var current = document.getElementsByClassName("active");
                current[0].className = current[0].className.replace(" active", "");
                this.className += " active";
            });
            }
        },
        opencontct() {
            this.openContact = !this.openContact
        },
        logOut() {
          window.location.href = '/livechat/liveChatLogout';
        },
        getChats(page) {

            if(this.lastPage === 0 || this.lastPage >= page) {
                this.$http.get(this.urlApi+`dialogs?limit=30&page=${page}`)
                .then((res) => {

                    this.lastPage = res.data.pagination.last_page

                    if(res.data.data != "disabled") {
                        this.reference = true;
                        if( res.data.pagination.last_page >=  page) {
                                var dataRes = res.data.data;
                            /* for (var i in dataRes) {
                                    if(this.DialogID.includes(dataRes[i].id)) {
                                        dataRes.splice(i, 1);
                                    } 
                                } */
                                for (var p in res.data.pinnedConvs) {
                                    if(!this.DialogsBin.includes(res.data.pinnedConvs[p].id)) {
                                        this.chatsPin.push(res.data.pinnedConvs[p]);
                                        this.DialogsBin.push(res.data.pinnedConvs[p].id);
                                    }
                                } 

                                

                                this.chats.Dialogs = this.chats.Dialogs ? this.chats.Dialogs.concat(dataRes) : dataRes;
                            //  this.UserData = res.data.UserData;
                            // this.InstanceNumber = res.data.InstanceNumber;
                                this.loading = false;
                            }
                            else {
                                this.totalCount = true
                            }
                    
                    }  else {
                        this.reference = false;
                        this.openMain();
                    }
                    
                    
                });
            } else {
                this.totalCount = true
            }

             
        },
        getChatsClick() {
            this.openMainMsgs = false;
            this.chatsPin = [];
            this.DialogID = [];
            this.newMsg = [];
            this.load = 1;
            this.chats.Dialogs = [];
            this.loading = true;
           this.$http.get(this.urlApi+`dialogs?limit=30&page=1`)
            .then((res) => {
                this.chatsPin = [];
                this.DialogID = [];
                this.newMsg = [];
                this.load = 1;
                this.chats.Dialogs = [];
                var dataRes = res.data.data;
                for (var i in dataRes) {
                    if(this.DialogID.includes(dataRes[i].id)) {
                        dataRes.splice(i, 1);
                    } 

                    if(dataRes[i].is_pinned === 1){
                        this.chatsPin.push(dataRes[i]);
                    }
                    if(dataRes[i].id === this.chatId) {
                        dataRes[i].is_read = 0;
                        dataRes[i].unreadCount = 0;
                    }
                }   
                this.chats.Dialogs = [];
                this.chats.Dialogs =  dataRes;
                this.loading = false;
            })
        },
        loadMore() {
            this.load++;
            this.getChats(this.load);
        },
        search() {
            this.loading = true;
            if(this.searchTo !== "") {
                this.$http.get(this.urlApi+`dialogs?name=${this.searchTo}`).then((res) => {
                    this.searchDiv =  res.data.data;
                    this.loading = false
                });
            } else  {
                this.searchDiv = [];
                this.loading = false
            }
        },
        getId(id) {
            this.$store.dispatch("chatIdAction", {id:id});
        },
        searchPucher(srch) {

                var data = srch;

                this.DialogID.push(data.id);
                this.DialogID = [ ...new Set(this.DialogID) ]; 

                for (var i in this.newMsg) {

                    if(this.newMsg[i].id === data.id) {
                        this.newMsg.splice(i, 1);
                        break;
                    }  
                } 

                this.newMsg.unshift(data);

                if (data.is_pinned === 1) {
                
                    for (var c in this.chatsPin) {
                        if(this.chatsPin[c].id === data.id) {
                            this.chatsPin.splice(c, 1);
                            break;
                        }  
                    } 
                    this.chatsPin.unshift(data);
                }

                if(this.searchDiv !== [] && this.openMainMsgs === false && this.searchTo !== "") {
                    for (var s in this.searchDiv) {
                            if(this.searchDiv[s].id === data.id) {
                                this.searchDiv.splice(s, 1);
                                break;
                            }  
                        } 
                    this.searchDiv.unshift(data);
                    
                }
                
                if(data.id === this.chatId) {
                    data.unreadCount = 0;
                }
        

        },
        changeName() {
            //console.log("test");
            if(this.getContact.length !== 0) {
                for (var i in this.chats.Dialogs) {
                //    console.log(this.getContact.id +" - "+  this.chats.Dialogs[i].id)
                    if(this.getContact.id ===  this.chats.Dialogs[i].id) {
                        this.chats.Dialogs[i].chatName = this.getContact.chatName
                    } 
                }  
                for (var c in this.chatsPin) {
                    if(this.getContact.id ===  this.chatsPin[c].id) {
                        this.chatsPin[c].chatName = this.getContact.chatName
                    } 
                }   
                for (var s in this.searchDiv) {
                    if(this.getContact.id ===  this.searchDiv[s].id) {
                        this.searchDiv[s].chatName = this.getContact.chatName
                    } 
                }   
                for (var y in this.newMsg) {
                    if(this.getContact.id ===  this.newMsg[y].id) {
                        this.newMsg[y].chatName = this.getContact.chatName
                    } 
                }   
            }
        },
        changeMods() {
            //console.log("test");
            if(this.getContact.length !== 0) {
                for (var i in this.chats.Dialogs) {
                //    console.log(this.getContact.id +" - "+  this.chats.Dialogs[i].id)
                    if(this.getContact.id ===  this.chats.Dialogs[i].id) {
                        this.chats.Dialogs[i].moderators = this.moderatorsContact
                    } 
                }  
                for (var c in this.chatsPin) {
                    if(this.getContact.id ===  this.chatsPin[c].id) {
                        this.chatsPin[c].moderators = this.moderatorsContact
                    } 
                }   
                for (var s in this.searchDiv) {
                    if(this.getContact.id ===  this.searchDiv[s].id) {
                        this.searchDiv[s].moderators = this.moderatorsContact
                    } 
                }   
                for (var y in this.newMsg) {
                    if(this.getContact.id ===  this.newMsg[y].id) {
                        this.newMsg[y].moderators = this.moderatorsContact
                    } 
                }   
            }
        },
        changeLabels() {
            for (var i in this.chats.Dialogs) {
                if(this.getContact.id ===  this.chats.Dialogs[i].id) {
                    this.chats.Dialogs[i].Labels = this.getContact.Labels;
                    this.chats.Dialogs[i].labelsColors = this.getContact.labelsColors;
                    this.chats.Dialogs[i].labelsText = this.getContact.labelsText;
                } 
            }   
            for (var c in this.chatsPin) {
                if(this.getContact.id ===  this.chatsPin[c].id) {
                    this.chatsPin[c].Labels = this.getContact.Labels;
                    this.chatsPin[c].labelsColors = this.getContact.labelsColors;
                    this.chatsPin[c].labelsText = this.getContact.labelsText;
                } 
            }  
            for (var s in this.searchDiv) {
                if(this.getContact.id ===  this.searchDiv[s].id) {
                    this.searchDiv[s].Labels = this.getContact.Labels;
                    this.searchDiv[s].labelsColors = this.getContact.labelsColors;
                    this.searchDiv[s].labelsText = this.getContact.labelsText;
                } 
            }  
            for (var y in this.newMsg) {
                if(this.getContact.id ===  this.newMsg[y].id) {
                    this.newMsg[y].Labels = this.getContact.Labels;
                    this.newMsg[y].labelsColors = this.getContact.labelsColors;
                    this.newMsg[y].labelsText = this.getContact.labelsText;
                } 
            }   
        },
        changeNameMethod(newVal){
            this.newNameContact = newVal
        },
        changeModsMethod(newVal){
            this.moderatorsContact = newVal
        },
        changePin() {
/*
            for (var c in this.chatsPin) {
                if(this.getContact.id ===  this.chatsPin[c].id) {
                    this.chatsPin.splice(c, 1);
                } 
            }   
            for (var i in this.chats.Dialogs) {
                if(this.getContact.id ===  this.chats.Dialogs[i].id) {
                    this.chats.Dialogs[i].is_pinned = this.getContact.is_pinned;
                    if(this.getContact.is_pinned === 1) {
                        this.chatsPin.unshift(this.chats.Dialogs[i]);
                    }
                    
                } 
            }  
            if(this.newMsg) {
                for (var y in this.newMsg) {
                    if(this.getContact.id ===  this.newMsg[y].id) {
                        this.newMsg[y].is_pinned = this.getContact.is_pinned;
                        if(this.getContact.is_pinned === 1) {
                            this.chatsPin.unshift(this.newMsg[y]);
                        }
                    } 
                }   
            }
            for (var s in this.searchDiv) {
                if(this.getContact.id ===  this.searchDiv[s].id) {
                    this.searchDiv[s].is_pinned = this.getContact.is_pinned;
                } 
            }  
            */
        },
        openMain() {
            this.openMainMsgs = true;
            this.load = 1;
            this.loading = true;
            this.searchTo = "";
            this.chatsPin = [];
            this.DialogID = [];
            this.searchDiv = [];
            this.chats.Dialogs = [];
            this.newMsg = [];
            this.$http.get(this.urlApi+`dialogs?mine=3`)
            .then((res) => {
                var dataRes = res.data.data;
                this.chatsPin = [];
                this.DialogID = [];
                this.searchDiv = [];
                this.chats.Dialogs = [];
                this.newMsg = [];
                
                for (var i in dataRes) {
                    if(this.DialogID.includes(dataRes[i].id)) {
                        dataRes.splice(i, 1);
                    } 
                    if(dataRes[i].is_pinned === 1){
                        this.chatsPin.push(dataRes[i]);
                    }
                    if(dataRes[i].id === this.chatId) {
                        dataRes[i].is_read = 0;
                        dataRes[i].unreadCount = 0;
                    }
                }
                this.chats.Dialogs = dataRes;
                
                this.loading = false;
            });
        }
    },
    computed:{
        chatId() {
            return this.$store.getters.chatId;
        },
        getContact() {
            return this.$store.getters.contact;
        },
        urlApi() {
            return this.$store.getters.urlApi;
        },
        domain() {
            return this.$store.getters.domain;
        }
    },
    components:{
        ListChats,
        chatComponent,
        Contact
    }
}
</script>
<style scoped>

.logo-sm img
{
    width: 35px;
    height: 35px;
    border-radius: 50%;
    margin-top: -9px;
}

.side-menu-nav .nav-item
{
    display:block;
    width:100%;
}

.side-menu-nav .nav-item .nav-link.active
{
    color:#1bc5bd
}

.startChat
{
    display:flex;
    justify-content:center;
    align-items: center;
    background-color:#f7f7ff;
    position:relative
}

.startChat img
{
    width:200px;
    opacity: 0.6;
    margin-bottom:15px;
}

.startChat:before
{
    content:"";
    position:absolute;
    left:0;
    bottom:0;
    width:100%;
    height:10px;
    background-color:#63dad7
}

</style>