<template>
    <!-- Start User chat -->
    <div class="user-chat w-100 overflow-hidden user-chat-show">
        <div class="d-lg-flex">
            <transition name="fade">
                <image-viewer-vue
                    v-if="imageViewerFlag === contact.image"
                    :imgUrlList="image"
                    :title="contact.name"
                    :closable="true"
                    :cyclical="true"
                    draggable="false"
                    :alt="'Not Found'"
                    @closeImageViewer="imageViewerFlag = false"
                >
                </image-viewer-vue>
            </transition>
            <!-- start chat conversation section -->
            <div class="w-100 ChatDiv" @dragover="dragover" @dragleave="dragleave" @drop="drop">
                <div class="p-3 chatHead border-bottom">
                    <div class="row align-items-center">
                        <div class="col-sm-4 col-8">
                            <div class="media align-items-center">
                                <div class="ml-3">

                                    <img
                                    @click="imageViewer(contact.image)"
                                    onError="this.onerror=null;this.src='https://whatsloop.net/resources/Gallery/UserDefault.png';"
                                    class="rounded-circle avatar-xs"
                                    :src="
                                        contact.image
                                        ? contact.image
                                        : 'https://whatsloop.net/resources/Gallery/UserDefault.png'
                                    "
                                    />
                                </div>
                                <div class="media-body">
                                    <h5 class="font-size-16 mb-0 text-truncate"><a @click="opencontc()" class="text-reset user-profile-show">
                                    <span v-if="contact.chatName != ''">{{ contact.chatName }}</span>
                                    <span v-else>{{ contact.id | removeUs }}</span>
                                    </a></h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-8 col-4">
                            <ul class="list-inline user-chat-nav text-left mb-0">
                                <li class="list-inline-item  d-lg-inline-block">
                                    <button @click="openCht()" type="button" class="d-lg-none user-chat-remove btn nav-btn">
                                        <i class="user-chat-remove ri-arrow-left-s-line"></i>
                                    </button>
                                </li>
                                <li class="list-inline-item  d-lg-inline-block">
                                    <button @click="opencontc()" type="button" class="btn nav-btn user-profile-show">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"><path fill="currentColor" d="M12 7a2 2 0 1 0-.001-4.001A2 2 0 0 0 12 7zm0 2a2 2 0 1 0-.001 3.999A2 2 0 0 0 12 9zm0 6a2 2 0 1 0-.001 3.999A2 2 0 0 0 12 15z"></path></svg>
                                    </button>
                                </li>
                            </ul>

                            
                        </div>
                    </div>
                </div>
                <!-- end chat user head -->

                <!-- start chat conversation -->
                
                <div class="chat-conversation p-2 p-lg-3" ref="vs2" data-simplebar="init">
                    <div class="bg"></div>
                            <vuescroll @handle-scroll="handleScroll" ref="vs">
                                <div class="loading" v-if="!loading">
                                    <svg class="spinner-container" viewBox="0 0 44 44">
                                        <circle class="path" cx="22" cy="22" r="20" fill="none" stroke-width="4"></circle>
                                    </svg>
                                </div>
                                <ul id="listChat" class="list-unstyled clearfix mb-0" v-if="chat !== []" >
                                   <!-- <li>
                                        <div class="chat-day-title">
                                                <span class="title">Today</span>
                                        </div>
                                    </li>-->
                                    <li v-for="(message, index) in chat"
                                    :key="index"
                                    class="clearfix"
                                    :class="message.fromMe  ? 'right' : ''" :id="message.id">
                                       <div class="chat-day-title" v-if="message.isToday">
                                            <span class="title">{{ message.created_at_day }}</span>
                                            <div class="clearfix"></div>
                                        </div>
                                        <div class="conversation-list">
                                            <!--<div class="chat-avatar">

                                                <img onerror="this.onerror=null;this.src='https://whatsloop.net/app/chatvia/assets/images/logo.svg';"
                                                :src="message.profile" alt="test">
                                            </div>-->

                                            <div class="user-chat-content">
                                                <div class="ctext-wrap">
                                                    <div class="ctext-wrap-content" :class="{ msgRemoved: !message.message && !message.fileSize,'testMsg' : message.testMsg = true,quoted:message.quotedMsgObj }">
                                                        <!-- fromMe withImage -->
                                                        <div class="options" v-if="message.id && message.whatsAppMessageType !== 'call_log'"  :class="'options'+index">
                                                            <i @click="openOptions(index)" class="fa fa-angle-down openOptions"></i>
                                                            <ul class="listOptions" :class="'listOptions'+index">
                                                                <li @click="reply(message)">رد</li>
                                                            </ul>
                                                        </div>
                                                        <!-- withImage:message.quotedMsgBody !== '' -->
                                                        <div class="replyMsg" v-if="message.quotedMsgObj" @click="replyClick(message.quotedMsgObj.id)" :class="{fromMe:message.quotedMsgObj.fromMe === 1,withImage:message.quotedMsgObj.whatsAppMessageType === 'image'}">
                                                            <span class="text-truncate replyName">
                                                            
                                                                <template v-if="message.quotedMsgObj.fromMe === 1">
                                                                    انت 
                                                                </template>
                                                                <template v-else>
                                                                {{ message.quotedMsgObj.chatName }}
                                                                </template>
                                                            </span>
                                                            <span  class="msg" v-if="message.quotedMsgObj.whatsAppMessageType === 'chat' && message.quotedMsgObj.status !== 'BOT PLUS' || message.quotedMsgObj.whatsAppMessageType === 'link' &&  message.quotedMsgObj.status !== 'BOT PLUS' || message.quotedMsgObj.whatsAppMessageType === 'buttons_response' &&  message.quotedMsgObj.status !== 'BOT PLUS'"> 
                                                            {{ message.quotedMsgObj.body }}
                                                            </span>

                                                             <span class="text-truncate msg" v-else-if="message.quotedMsgObj.status === 'BOT PLUS'">
                                                                <div class="optionsBotStyle" :dir="isUnicode(message.body) ? 'rtl' : 'ltr'"> 
                                                                        <div class="head">
                                                                            <h2 class="boldTitle">{{message.quotedMsgObj.body}}</h2>
                                                                            <h3 class="title">{{message.quotedMsgObj.metadata.title}}</h3>
                                                                            <span class="footerMsg">{{message.quotedMsgObj.metadata.footer}}</span>
                                                                        </div>
                                                                </div>
                                                            </span>

                                                            <span class="text-truncate msg" v-else-if="message.quotedMsgObj.whatsAppMessageType === 'image'">
                                                                صورة 
                                                                <i class="fa fa-camera"></i> 
                                                                <img onerror="this.onerror=null;this.src='https://whatsloop.net/resources/Gallery/UserDefault.png';" class="imgrelpy" :src="message.quotedMsgObj.body" />
                                                            </span>
                                                    
                                                            <span class="text-truncate msg" v-else-if="message.quotedMsgObj.whatsAppMessageType === 'video'">
                                                                فيديو
                                                                <i class="fa fa-image"></i> 
                                                            </span>
                                                        

                                                             <span class="text-truncate msg" v-else-if="message.quotedMsgObj.whatsAppMessageType === 'document'">
                                                                ملف 
                                                                <i class="fa fa-file"></i> 
                                                            </span>

                                                            <span class="text-truncate msg" v-else-if="message.quotedMsgObj.whatsAppMessageType === 'location'">
                                                                موقع 
                                                                <i class="fa fa-map-marker"></i> 
                                                            </span>

                                                            <span class="text-truncate msg" v-else-if="message.quotedMsgObj.whatsAppMessageType === 'contact' || message.quotedMsgObj.whatsAppMessageType === 'vcard'">
                                                                <i class="fa fa-user"></i> 
                                                                {{message.quotedMsgObj.contact_name}}
                                                            </span>

                                                            <span class="text-truncate msg" v-else-if="message.quotedMsgObj.whatsAppMessageType === 'order'">
                                                                {{message.quotedMsgObj.orderDetails.name}}
                                                                <svg width="24" height="24" viewBox="0 0 24 24" class="svgStore"><g fill="none" fill-rule="evenodd"><path d="M3.555 5.111h16.888V3H3.555v2.111zm0 1.057L2.5 11.447v2.111h1.055v6.332H14.11v-6.332h4.224v6.332h2.111v-6.332H21.5v-2.111l-1.055-5.28H3.555zM5.666 17.78h6.332v-4.223H5.666v4.223z" id="Page-1-Copy" fill="currentColor"></path></g></svg>
                                                                
                                                            </span>
                                                            
                                                            <span class="text-truncate msg" v-else-if="message.quotedMsgObj.whatsAppMessageType === 'product'">
                                                                {{message.quotedMsgObj.productDetails.name}}
                                                                <svg width="24" height="24" viewBox="0 0 24 24" class="svgStore"><g fill="none" fill-rule="evenodd"><path d="M3.555 5.111h16.888V3H3.555v2.111zm0 1.057L2.5 11.447v2.111h1.055v6.332H14.11v-6.332h4.224v6.332h2.111v-6.332H21.5v-2.111l-1.055-5.28H3.555zM5.666 17.78h6.332v-4.223H5.666v4.223z" id="Page-1-Copy" fill="currentColor"></path></g></svg>
                                                                <img onerror="this.onerror=null;this.src='https://whatsloop.net/resources/Gallery/UserDefault.png';" class="imgrelpy" 
                                                                :src="message.quotedMsgObj.productDetails.mainImage" />

                                                            </span>

                                                            <span class="text-truncate msg" v-else-if="message.quotedMsgObj.whatsAppMessageType === 'ptt' || message.quotedMsgObj.whatsAppMessageType === 'ppt'">
                                                                مقطع صوتي
                                                                <i class="fa fa-microphone"></i> 
                                                            </span>

                                                            <span class="text-truncate msg" v-else-if="message.quotedMsgObj.whatsAppMessageType === 'call_log'">
                                                                <i class="fa fa-phone"></i> 
                                                                مكالمة
                                                                
                                                            </span>
                                                        </div>
                                                        
                                                        <p v-if="message.whatsAppMessageType === 'chat' && message.status !== 'BOT PLUS' || message.whatsAppMessageType === 'link'  && message.status !== 'BOT PLUS' || message.whatsAppMessageType === 'buttons_response'  && message.status !== 'BOT PLUS' " class="m-1"  :dir="isUnicode(message.body) ? 'rtl' : 'ltr'">
                                                            <template v-if="message.body.includes('*') || message.body.includes('https://whatsloop.net/resources/Gallery/') || !message.body.includes('iframe')">
                                                                <p :inner-html.prop="message.body | removeAst | readLinks">
                                                                </p>
                                                            </template>
                                                            <template v-else-if="!message.body.includes('iframe')">
                                                            
                                                                <p :inner-html.prop="message.body | readLinks">
                                                                </p>
                                                            </template>
                                                            <template v-else>
                                                            
                                                                <p>{{ message.body }}</p>
                                                            </template>
                                                        </p>

                                                        <div v-else-if="message.whatsAppMessageType === 'chat' && message.status === 'BOT PLUS' " class="optionsBotStyle" :dir="isUnicode(message.body) ? 'rtl' : 'ltr'"> 
                                                                <div class="head">
                                                                    <h2 class="boldTitle">{{message.body}}</h2>
                                                                    <h3 class="title">{{message.metadata.title}}</h3>
                                                                    <span class="footerMsg">{{message.metadata.footer}}</span>
                                                                </div>
                                                        </div>
                                                        
                                                        <p v-else-if="message.whatsAppMessageType === 'image'" :dir="isUnicode(message.caption) ? 'rtl' : 'ltr'">
                                                            <transition name="fade">
                                                                <image-viewer-vue v-if="imageViewerFlag === message.body"
                                                                    @closeImageViewer="imageViewerFlag = false"
                                                                    :imgUrlList="message.body.split()"
                                                                    :closable="true"
                                                                    :cyclical="true"
                                                                >
                                                                </image-viewer-vue>
                                                            </transition>
                                                            <img
                                                            @click="imageViewer(message.body)"
                                                            onerror="this.onerror=null;this.src='https://whatsloop.net/resources/Gallery/UserDefault.png';"
                                                            class="imgMsg"
                                                            :src="
                                                                message.body
                                                                ? message.body
                                                                : 'https://whatsloop.net/resources/Gallery/UserDefault.png'
                                                            "
                                                            />

                                                            <span v-if="message.caption">
                                                                <template v-if="message.caption.includes('*') || message.caption.includes('https://whatsloop.net/resources/Gallery/') && !message.caption.includes('iframe')">
                                                                    <p :inner-html.prop="message.caption | removeAst">
                                                                    </p>
                                                                </template>
                                                                <template v-else-if="!message.caption.includes('iframe')">
                                                                    <p :inner-html.prop="message.caption | readLinks">
                                                                    </p>
                                                                </template>
                                                                <template v-else>
                                                                    <p>{{ message.caption }}</p>
                                                                </template>
                                                            </span>
                                                        </p>

                                                        <p v-else-if="message.whatsAppMessageType === 'video'" :dir="isUnicode(message.caption) ? 'rtl' : 'ltr'">
                                                            
                                                            <video class="videoStyle" controls>
                                                                <source :src="message.body" type="video/mp4">
                                                                Your browser does not support the video.
                                                            </video>

                                                            <span v-if="message.caption">
                                                                <template v-if="message.caption.includes('*') || message.caption.includes('https://whatsloop.net/resources/Gallery/') && !message.caption.includes('iframe')">
                                                                    <p :inner-html.prop="message.caption | removeAst">
                                                                    </p>
                                                                </template>
                                                                <template v-else-if="!message.caption.includes('iframe')">
                                                                    <p :inner-html.prop="message.caption | readLinks">
                                                                    </p>
                                                                </template>
                                                                <template v-else>
                                                                    <p>{{ message.caption }}</p>
                                                                </template>
                                                            </span>
                                                        </p>


                                                        <div v-else-if="message.whatsAppMessageType === 'location'" class="m-1 mapIframe">
                                                            <a target="_blank" :href="'https://www.google.com/maps/search/' + message.MapAddress + '/@' + message.MapLatitude + ',' + message.MapLongitude +',17z?hl=ar'"  ></a>
                                                            <iframe :src="'https://www.google.com/maps/embed/v1/place?q=' + message.MapLatitude + ',' + message.MapLongitude + '&key=AIzaSyCai_Ru6iTKHQjlKrihzsRh_-kz5nRNxGw'"
                                                            width='260' height='200' frameborder='0' style='border:0;' allowfullscreen='' aria-hidden='false' tabindex='0'></iframe>
                                                            <span class="mapMessage text-truncate">{{ message.MapAddress }}</span>
                                                        </div>
                                                        
                                                        <div v-else-if="message.whatsAppMessageType === 'document'">
                                                        
                                                            <!-- end card -->
                                                            <span v-if="message.MessageFileCaption">
                                                                <template v-if="message.MessageFileCaption.includes('*') || message.MessageFileCaption.includes('https://whatsloop.net/resources/Gallery/') && !message.MessageFileCaption.includes('iframe')">
                                                                    <p :inner-html.prop="message.MessageFileCaption | removeAst">
                                                                    </p>
                                                                </template>
                                                                <template v-else-if="!message.MessageFileCaption.includes('iframe')">
                                                                    <p :inner-html.prop="message.MessageFileCaption | readLinks">
                                                                    </p>
                                                                </template>
                                                                <template v-else>
                                                                    <p>{{ message.MessageFileCaption }}</p>
                                                                </template>
                                                            </span>
                                                            <div class="card p-2 border mb-2">
                                                                <div class="media align-items-center">
                                                                    <div class="avatar-sm mr-3">
                                                                        <div class="avatar-title bg-soft-primary text-primary rounded font-size-20">
                                                                            <i style="color:#495057" class="fa fa-file"></i>
                                                                        </div>
                                                                    </div>
                                                                    <div class="media-body">
                                                                        <div class="text-left">
                                                                            <h5 class="font-size-14 mb-1 text-truncate" >File.{{message.file_name.split(".")[1]}}</h5>
                                                                        </div>
                                                                    </div>

                                                                    <div class="ml-4">
                                                                        <ul class="list-inline mb-0 font-size-18">
                                                                            <li class="list-inline-item"  v-if="message.body != undefined">
                                                                                <a :href="message.body"  :download="message.file_name" target="_blank" class="text-muted px-1">
                                                                                    <i class="ri-download-2-line"></i>
                                                                                </a>
                                                                            </li>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <!-- end card -->
                                                       </div>


                                                        <span v-else-if="message.whatsAppMessageType === 'vcard' || message.whatsAppMessageType === 'contact'" class="contactSend"> 
                                                                <i class="UserIcon fa fa-user"></i>
                                                                <p class="contactName text-truncate">{{message.contact_name}}</p>
                                                                <p class="contactPhone">{{message.contact_number}}</p>
                                                        </span>

                                                        <span v-else-if="message.whatsAppMessageType === 'order'" class="productStyle"> 
                                                                <div class="head">
                                                                    <img class="productImg" src="https://whatsloop.net/resources/Gallery/UserDefault.png"  />

                                                                    <p class="productQuantity text-truncate"><i class="fa fa-shopping-cart"></i> {{message.orderDetails.quantity}}</p>
                                                                    <p class="productPrice text-truncate">{{ message.orderDetails.price }}</p>
                                                                </div>
                                                                <p class="productName text-truncate">{{ message.orderDetails.name }} </p>
                                                                <a class="productBtn text-truncate" target="_blank" :href="message.orderDetails.url">مشاهدة العربة</a>
                                                        </span>

                                                        <span v-else-if="message.whatsAppMessageType === 'product'" class="productStyle productStyle2"> 
                                                            <transition name="fade">
                                                                <image-viewer-vue v-if="imageViewerFlag === message.productDetails.mainImage"
                                                                    @closeImageViewer="imageViewerFlag = false"
                                                                    :imgUrlList="message.productDetails.mainImage.split()"
                                                                    :closable="true"
                                                                    :cyclical="true"
                                                                >
                                                                </image-viewer-vue>
                                                            </transition>
                                                                <img
                                                                @click="imageViewer(message.productDetails.mainImage)"
                                                                onerror="this.onerror=null;this.src='https://whatsloop.net/resources/Gallery/UserDefault.png';"
                                                                class="imgMsg"
                                                                :src="
                                                                    message.productDetails.mainImage
                                                                    ? message.productDetails.mainImage
                                                                    : 'https://whatsloop.net/resources/Gallery/UserDefault.png'
                                                                "
                                                                />
                                                                <div class="head">
                                                                    <p class="productName text-truncate">{{ message.productDetails.name }} </p>
                                                                    <p class="productPrice text-truncate">{{ message.productDetails.price }} {{ message.productDetails.currency }}</p>
                                                                </div>
                                                                
                                                        </span>

                                                        <span v-else-if="message.whatsAppMessageType === 'ptt' || message.quotedMsgObj.whatsAppMessageType === 'ppt'"> 
                                                            <audioplayer :timesec="message.timeSec ? message.timeSec : false" :url="message.body" 
                                                            v-on:pauseall="funcPause('audio-player'+ message.time )"
                                                            :playerid="'audio-player'+message.time" ref="component" ></audioplayer>                                                                 
                                                        </span>

                                                        <span v-else-if="message.whatsAppMessageType === 'call_log'"> 
                                                            <template v-if="message.fromMe == 1">
                                                                <span><i style="color:#0f0;margin-left:3px;float:right;margin-top:3px" class="fa fa-phone"></i> مكالمة صادرة </span>
                                                            </template>
                                                            <template v-else>
                                                                <span><i  style="color:#f00;margin-left:3px;float:right;margin-top:3px" class="fa fa-phone"></i> مكالمة واردة </span>
                                                            </template>
                                                            
                                                        </span>
                         
                                                        <p v-else>
                                                         <i class="fa fa-ban"></i> 
                                                        رسالة محذوفة أو غير مدعومة
                                                        </p>
                                                       
                                                        <p class="chat-time clearfix mb-0">
                                                            <span class="seen">
                                                                <svg
                                                                    v-if="message.fromMe === 1 && message.sending_status == 0"
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
                                                                    v-else-if="message.fromMe === 1 && message.sending_status == 1"
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
                                                                    v-else-if="message.fromMe === 1 && message.sending_status == 2"
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
                                                                    v-else-if="message.fromMe === 1 && message.sending_status == 3"
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
                                                            <span class="align-middle">{{ message.created_at_time }}</span>

                                                        
                                                        </p>
                                                    </div>
                                                </div>
                                                
                                                <ul class="listBotBtns" v-if="message.metadata !== 'null' && message.metadata.replyButtons">
                                                    <li v-for="(replyBtns, index) in message.metadata.replyButtons" :key="index" @click="sendReplyBtns(replyBtns.displayText)"> {{ replyBtns.displayText }}</li>
                                                </ul>
                                                <div class="conversation-name" v-if="message.fromMe === 1">{{ message.status }}</div>


                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </vuescroll>
                            <i class="fa fa-angle-down goDown" :class="{'active' : goTop}" @click="goDown(0)"><span v-if="totalNewMsg > 0">{{ totalNewMsg }}</span></i>

                    
                </div>
                <!-- end chat conversation end  -->
                <div class="replyContainer" v-if="replyArry !== null">
                    <i class="closeReply" @click="closeReply()">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"><path fill="currentColor" d="M19.1 17.2l-5.3-5.3 5.3-5.3-1.8-1.8-5.3 5.4-5.3-5.3-1.8 1.7 5.3 5.3-5.3 5.3L6.7 19l5.3-5.3 5.3 5.3 1.8-1.8z"></path></svg>
                    </i>
                    <div class="replyMsg" :class="{fromMe:replyArry.fromMe === 1,withImage:replyArry.whatsAppMessageType === 'image'}">
                        <span class="text-truncate replyName">
                            <template v-if="replyArry.fromMe === 1">
                                انت 
                            </template>
                            <template v-else>
                            {{ replyArry.chatName }}
                            </template>
                        </span>
                        <p class="text-truncate msg m-1" v-if="replyArry.whatsAppMessageType === 'chat' && replyArry.status !== 'BOT PLUS' || replyArry.whatsAppMessageType === 'buttons_response' && replyArry.status !== 'BOT PLUS'" :dir="isUnicode(replyArry.body) ? 'rtl' : 'ltr'">
                            <template v-if="replyArry.body.includes('*') || replyArry.body.includes('https://whatsloop.net/resources/Gallery/') || !replyArry.body.includes('iframe')">
                                <p :inner-html.prop="replyArry.body | removeAst">
                                </p>
                            </template>
                            <template v-else>
                            
                                <p>{{ replyArry.body }}</p>
                            </template>
                        </p>


                        <span class="text-truncate msg" v-else-if="replyArry.status === 'BOT PLUS'">
                            <div class="optionsBotStyle" :dir="isUnicode(replyArry.body) ? 'rtl' : 'ltr'"> 
                                    <div class="head">
                                        <h2 class="boldTitle">{{replyArry.body}}</h2>
                                        <h3 class="title">{{replyArry.metadata.title}}</h3>
                                    </div>
                            </div>
                        </span>


                        <span class="text-truncate msg" v-else-if="replyArry.whatsAppMessageType === 'image'">
                            صورة 
                            <i class="fa fa-camera"></i> 
                            <img onError="this.onerror=null;this.src='https://whatsloop.net/resources/Gallery/UserDefault.png';" class="imgrelpy" :src="replyArry.body" />
                        </span>
                
                        <span class="text-truncate msg" v-else-if="replyArry.whatsAppMessageType === 'video'">
                            محتوي مرئي 
                            <i class="fa fa-image"></i> 
                        </span>

                        <span class="text-truncate msg" v-else-if="replyArry.whatsAppMessageType === 'document'">
                            ملف 
                            <i class="fa fa-file"></i> 
                        </span>

                        <span class="text-truncate msg" v-else-if="replyArry.whatsAppMessageType === 'location'">
                            موقع 
                            <i class="fa fa-map-marker"></i> 
                        </span>

                        <span class="text-truncate msg" v-else-if="replyArry.whatsAppMessageType === 'contact' || replyArry.whatsAppMessageType === 'vcard'">
                            
                            <i class="fa fa-user"></i> 
                            {{replyArry.contact_name}}
                        </span>

                        <span class="text-truncate msg" v-else-if="replyArry.whatsAppMessageType === 'order'">
                            {{replyArry.orderDetails.name}}
                            <svg width="24" height="24" viewBox="0 0 24 24" class="svgStore"><g fill="none" fill-rule="evenodd"><path d="M3.555 5.111h16.888V3H3.555v2.111zm0 1.057L2.5 11.447v2.111h1.055v6.332H14.11v-6.332h4.224v6.332h2.111v-6.332H21.5v-2.111l-1.055-5.28H3.555zM5.666 17.78h6.332v-4.223H5.666v4.223z" id="Page-1-Copy" fill="currentColor"></path></g></svg>
                            
                        </span>
                        <span class="text-truncate msg" v-else-if="replyArry.whatsAppMessageType === 'product'">
                            {{replyArry.productDetails.name}}
                            <svg width="24" height="24" viewBox="0 0 24 24" class="svgStore"><g fill="none" fill-rule="evenodd"><path d="M3.555 5.111h16.888V3H3.555v2.111zm0 1.057L2.5 11.447v2.111h1.055v6.332H14.11v-6.332h4.224v6.332h2.111v-6.332H21.5v-2.111l-1.055-5.28H3.555zM5.666 17.78h6.332v-4.223H5.666v4.223z" id="Page-1-Copy" fill="currentColor"></path></g></svg>
                            <img onerror="this.onerror=null;this.src='https://whatsloop.net/resources/Gallery/UserDefault.png';" class="imgrelpy" 
                            :src="replyArry.productDetails.mainImage" />

                        </span>



                        <span class="text-truncate msg" v-else-if="replyArry.whatsAppMessageType === 'ptt' || message.quotedMsgObj.whatsAppMessageType === 'ppt'">
                            مقطع صوتي
                            <i class="fa fa-microphone"></i> 
                        </span>

                        <span class="text-truncate msg" v-else-if="replyArry.whatsAppMessageType === 'call_log'">
                            مكالمة
                            <i class="fa fa-phone"></i> 
                        </span>
                    </div>
                </div>

                <twemoji-textarea class="emojMob"
                    :emojiData="emojiDataAll" 
                    :emojiGroups="emojiGroups" 
                    :content.sync="emoj"
                    :searchEmojisFeat="true"
                    searchEmojiPlaceholder="ابحث عن إيموجي"
                    searchEmojiNotFound="لا يوجد نتائح بحث."
                    isLoadingLabel="جاري البحث..."
                    :pickerCloseOnClickaway="closeEmoji"
                    >
                    <template v-slot:twemoji-picker-button>
                        <button @click="closeEmoji = false; openQuick = false;goDown(500)">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"><path fill="currentColor" d="M9.153 11.603c.795 0 1.439-.879 1.439-1.962s-.644-1.962-1.439-1.962-1.439.879-1.439 1.962.644 1.962 1.439 1.962zm-3.204 1.362c-.026-.307-.131 5.218 6.063 5.551 6.066-.25 6.066-5.551 6.066-5.551-6.078 1.416-12.129 0-12.129 0zm11.363 1.108s-.669 1.959-5.051 1.959c-3.505 0-5.388-1.164-5.607-1.959 0 0 5.912 1.055 10.658 0zM11.804 1.011C5.609 1.011.978 6.033.978 12.228s4.826 10.761 11.021 10.761S23.02 18.423 23.02 12.228c.001-6.195-5.021-11.217-11.216-11.217zM12 21.354c-5.273 0-9.381-3.886-9.381-9.159s3.942-9.548 9.215-9.548 9.548 4.275 9.548 9.548c-.001 5.272-4.109 9.159-9.382 9.159zm3.108-9.751c.795 0 1.439-.879 1.439-1.962s-.644-1.962-1.439-1.962-1.439.879-1.439 1.962.644 1.962 1.439 1.962z"></path></svg>
                        </button>
                    </template>
                </twemoji-textarea>
                <!-- <i class="fa fa-close" @click="openQuick = !openQuick"></i> -->
                <div class="quickMsgs flex" v-if="openQuick">
                    
                    <vuescroll>
                        <ul>
                            <li class="chat-user-message text-truncate" v-for="rep in quickReplies" @click="sendQuick(rep.description_ar)" :key="rep.id">
                            <span class="title">{{ rep.name_ar }} :</span>
                                {{ rep.description_ar }}
                            </li>
                            
                        </ul>
                        <a target="_blank" :href="'http://'+domain+'.wloop.net/replies'" class="addRep"><i class="ri-add-circle-fill"></i> أضافة رد سريع</a>
                    </vuescroll>
                </div>
                <div class="quickMsgs mobile flex" v-if="openQuick">
                    
                    <ul>
                        <li class="chat-user-message text-truncate" v-for="rep in quickReplies" @click="sendQuick(rep.Content_ar)" :key="rep.id">
                        <span class="title">{{ rep.Title_ar }} :</span>
                            {{ rep.Content_ar }}
                        </li>
                        
                    </ul>
                    <a :href="'http://'+domain+'.wloop.net/replies'" class="addRep"><i class="ri-add-circle-fill"></i> أضافة رد سريع</a>
                </div>
                <!-- start chat input section -->
                <form @submit.prevent class="sendMessage">

                    <button class="openQuick ri-message-3-line"  v-b-tooltip.hover title="الردود السريعة" @click="closeQuick();closeEmoji = true" 
                    :class="classObject"></button>

                    <label class="uploadFile"  v-b-tooltip.hover title="تحميل صور او ملفات">
                        <i class="iconUpload" @click="upload()">
                            <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            width="24"
                            height="24"
                            >
                            <path
                                fill="currentColor"
                                d="M1.816 15.556v.002c0 1.502.584 2.912 1.646 3.972s2.472 1.647 3.974 1.647a5.58 5.58 0 0 0 3.972-1.645l9.547-9.548c.769-.768 1.147-1.767 1.058-2.817-.079-.968-.548-1.927-1.319-2.698-1.594-1.592-4.068-1.711-5.517-.262l-7.916 7.915c-.881.881-.792 2.25.214 3.261.959.958 2.423 1.053 3.263.215l5.511-5.512c.28-.28.267-.722.053-.936l-.244-.244c-.191-.191-.567-.349-.957.04l-5.506 5.506c-.18.18-.635.127-.976-.214-.098-.097-.576-.613-.213-.973l7.915-7.917c.818-.817 2.267-.699 3.23.262.5.501.802 1.1.849 1.685.051.573-.156 1.111-.589 1.543l-9.547 9.549a3.97 3.97 0 0 1-2.829 1.171 3.975 3.975 0 0 1-2.83-1.173 3.973 3.973 0 0 1-1.172-2.828c0-1.071.415-2.076 1.172-2.83l7.209-7.211c.157-.157.264-.579.028-.814L11.5 4.36a.572.572 0 0 0-.834.018l-7.205 7.207a5.577 5.577 0 0 0-1.645 3.971z"
                            ></path>
                            </svg>
                        </i>
                    </label>
                    <div class="textField" @click="focusInput()">
                    <span class="textV" v-if="messageSend === ''">كتابة رسالة </span>
                    <vuescroll>
                    <!-- .enter.prevent -->
                    
                        <div
                            @input="onInput"
                            class="post"
                            ref="textContent"
                            :dir="dir"
                            id="inputId"
                            @keydown="sendMsg"
                            tabindex="-1"
                            contenteditable="true"
                            type="text"
                        ></div>
                    </vuescroll>
                    </div>
                    <button class="btnSend" @click="sendFunc()" v-if="messageSend !== '' || checkFile == true" :disabled="messageSend === '' && checkFile === false">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"><path fill="currentColor" d="M1.101 21.757L23.8 12.028 1.101 2.3l.011 7.912 13.623 1.816-13.623 1.817-.011 7.912z"></path></svg>
                    </button>

                  
                    <vue-record-audio :closerec="closeRec" :mode="recordMode.video" @stream="onStreamRecord"  @result="onResultRecord" :class="messageSend === '' && checkFile === false ? 'block' : ''"/>
                   
                    <div class="counterRec" v-if="messageSend === '' && checkFile === false" :class="messageSend === '' ? 'block' : ''">
                        <label id="minutes"><span v-if="this.countRec.seconds < 10">0</span>{{this.countRec.seconds}}</label>
                        :
                        <label id="seconds"><span v-if="this.countRec.minutes < 10">0</span>{{this.countRec.minutes}}</label>
                    </div>
                    
                    <button @click="closeRecorder()" v-if="messageSend === '' && checkFile === false" :class="messageSend === '' ? 'block' : ''" class="closeRecord fa fa-close"></button>
                    
                </form>
                <vue-dropzone
                    class="dropDiv"
                    ref="myVueDropzone"
                    @vdropzone-removed-file="checkFile = false"
                    @vdropzone-file-added="checkFile = true"
                    acceptedFileTypes=".png,.jpg,.jpeg,.gif,.bmp,.txt,.pdf,.xlsx"
                    id="dropzone"
                    :useCustomSlot="true"
                    :options="dropzoneOptions"
                >
                    <div class="subtitle">اسحب الملفات وضعها هنا ليتم ارسالها</div>
                </vue-dropzone>
                <!-- end chat input section -->
            </div>
            <!-- end chat conversation section -->
        </div>
    </div>
    <!-- End User chat -->
</template>
<script>
import vuescroll from 'vuescroll';
import vue2Dropzone from "vue2-dropzone";

import {TwemojiTextarea} from '@kevinfaguiar/vue-twemoji-picker';

import audioplayer from './audioPlayer.vue'

import $ from 'jquery';


import EmojiAllData from '@kevinfaguiar/vue-twemoji-picker/emoji-data/en/emoji-all-groups.json';
import EmojiGroups from '@kevinfaguiar/vue-twemoji-picker/emoji-data/emoji-groups.json';

export default {
    name:"chatComponent",
    props:['chatId','openChat','opencontct',"contact","checkSubscription"],
    data() {
        return {
            chat: [],
            messageSend: "",
            dir: "",
            load: 1,
            newMsg: null,
            loading: false,
            distance: 500,
            imageViewerFlag: false,
            image:[],
            recordMode: {
                audio: 'hold',
                video: 'press'
            },
            closeRec:false,
            cancleRec:false,
            countRec:{
                minutes:0,
                seconds:0,
                totalSec:0
            },
            dropzoneOptions: {
                url: "https://httpbin.org/post",
                thumbnailWidth: 40,
                thumbnailHeight: 40,
                maxFilesize: 300.0,
                addRemoveLinks: true,
                dictRemoveFile: "<i class='fa fa-close'></i>",
                dictCancelUpload: "إلغاء الرفع",
                maxFiles: 29,
            },
            checkFile: false,
            openContact:false,
            totalNewMsg:0,
            goTop:false,
            messageDates:[],
            scroll:0,
            openQuick:false,
            quickReplies:[],
            closeEmoji:false,
            emoj:"",
            replyArry:null,
            limit:0
        };
    },
    watch:{
       chatIdC:{
           deep: true,
           handler() {
               if(this.chatIdC !== 0) {
                this.load = 0;
                this.getChatContent();
                this.focusInput();
                this.$store.dispatch("contactAction", {contact:this.contact});
                this.replyArry = null;
                }
            }
       },
       messageSend:{
           deep: true,
           handler() {
               this.openQuick = false;
               if(this.messageSend !== '') {
                   this.closeRecorder();
               }
               if(this.messageSend == "\n" || this.messageSend == "\n\n\n" || this.messageSend == "\n\n" || this.messageSend === " ") {
                    const inputId = document.getElementById("inputId");
                    inputId.textContent = '';
                   this.messageSend = "";
               } 
               else if(this.messageSend === "") {
                   this.dir = "rtl";
               }
               else if(this.messageSend === "/") {
                   this.openQuick = true;
               }

               
            }
       },
       emoj:{
           deep: true,
           handler() {
                const inputId = document.getElementById("inputId");
                const textarea = document.getElementById("twemoji-textarea1");
                inputId.textContent += this.emoj;
                this.messageSend += this.emoj;
                textarea.textContent = '';
                this.emoj = '';
            }
       },
       checkFile:{
           deep: true,
           handler() {
            this.cancleRec = true;
            this.closeRec = true;
                for (var i = 1; i < 99999; i++) {
                    window.clearInterval(i);
                }
                this.countRec.minutes = 0;
                this.countRec.seconds = 0;
                this.countRec.totalSec = 0;
            }
       }

    },
    mounted() {

       //  var hideMe = document.getElementsByClassName('options');
            document.onclick = function(e){
                var hasClass = Array.from(e.target.classList).indexOf('openOptions') > -1;
               if(!hasClass){
                   
                   setTimeout(function(){
                       $(".options").removeClass("active");
                   },500);
                   $(".listOptions").slideUp();
                 // hideMe.style.display = 'none';
               }
            };

        if(this.chatIdC !== 0) {
            
            document.getElementById('twemoji-textarea').id = "twemoji-textarea1";
            this.getChatContent();
            this.getQuick();
           
            var domain = window.location.host.split('.')[1] ? window.location.host.split('.')[0] : false;
            this.testBroadCastingSentMessage2(domain)
            this.testBroadCastingIncomingMessage2(domain);
            //this.testBroadCastingBotMessage2(domain);
            this.testBroadUpdateMessageStatus2(domain);


            var x = window.matchMedia("(min-width: 991px)")
            if(x.matches) {
                this.opencontc();
            }
        }

    },
    computed: {
        chatIdC() {
            return this.$store.getters.chatId;
        },
        chatName() {
            return this.$store.getters.chatName;
        },
        getContact() {
            return this.$store.getters.contact;
        },
        emojiDataAll() {
            return EmojiAllData;
        },
        emojiGroups() {
            return EmojiGroups;
        },
        classObject: function () {
            return {
                'active': this.openQuick,
                'hidMob': this.messageSend && this.messageSend !== '/'
            }
        },
        urlApi() {
            return this.$store.getters.urlApi;
        },
        domain() {
            return this.$store.getters.domain;
        }
    },
    methods: {
        testBroadCastingSentMessage2 (domain) {
        // Start socket.io listener
          window.Echo.channel(domain+'-NewSentMessage')
                .listen('SentMessage', (data) => {
                    if(this.chatIdC === data.message.id) {
                        setTimeout(() => {
                            var nnm = 0;
                            this.chat.forEach((element,index) => {
                                if (element.id === data.message.lastMessage.id) {
                                    nnm += 1;
                                } 
                                if (index === this.chat.length -1) {
                                    if(nnm === 0) {
                                        this.chat.push(data.message.lastMessage);
                                        if(this.$refs["vs"]) {
                                            const {v} = this.$refs["vs"].getScrollProcess();
                                            if(v > 0.85  ) {
                                                this.goDown(0)
                                            }
                                        }
                                    }
                                }
                            });
                        },500);
                    }
                })

          
          // End socket.io listener
        },
      testBroadCastingIncomingMessage2 (domain) {

        // Start socket.io listener
          window.Echo.channel(domain+'-NewIncomingMessage')
            .listen('IncomingMessage', (data) => {
             //  console.log(data)
                if(this.chatIdC === data.message.id) {
                    this.chat.push(data.message.lastMessage);
                    this.totalNewMsg += 1;
                    if(this.$refs["vs"]) {
                        const {v} = this.$refs["vs"].getScrollProcess();
                        if(v > 0.85  ) {
                            this.goDown(0)
                        }
                    }
                }
            });
          // End socket.io listener
      },/*
      testBroadCastingBotMessage2 (domain) {
        // Start socket.io listener
          window.Echo.channel(domain+'-NewBotMessage')
            .listen('BotMessage', (data) => {
                 console.log(data)
                    if(data.message.lastMessage.bot_details.reply_type == 2){
                        if(data.message.lastMessage.whatsAppMessageType == 'image'){
                            data.message.lastMessage.caption = data.message.lastMessage.bot_details.message;
                            data.message.lastMessage.body = data.message.lastMessage.bot_details.file;
                        }else{
                            data.message.lastMessage.caption = data.message.lastMessage.bot_details.file_name;
                        }
                    }
                    this.searchPucher(data.message);
                
            })
          // End socket.io listener
      },*/
    testBroadUpdateMessageStatus2 (domain) {
        // Start socket.io listener
          window.Echo.channel(domain+'-UpdateMessageStatus')
            .listen('MessageStatus', (data) => {
              //  console.log(data)
                if(this.chat !== []) {
                this.chat.forEach((element) => {
                if (element.id) {
                    if (element.id.search(data.messageId) !== -1) {
                    element.sending_status = data.statusInt;
                    }
                }
                });
                }
            })
          // End socket.io listener
      },
        handleScroll() {
            var element = document.getElementById("listChat");
            const { scrollTop } = this.$refs["vs"].getPosition();
            if(scrollTop === 0 && element.offsetHeight > 50) {
                this.loadMore();
            }

            const {v} = this.$refs["vs"].getScrollProcess();
            if(v < 0.85 && element.offsetHeight > 400 ) {
                this.goTop = true
            } else {
                 this.goTop = false;
                 this.totalNewMsg = 0;
            }
        },
        opencontc() {
            this.$emit("opencontct");
        },
        openCht() {
            this.$emit("openCht");
        },
        imageViewer(image) {
            this.image = [];
            this.image.push(image);
        this.imageViewerFlag = image;
        },
        isUnicode(str) {
            if(str) 
            {
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
            }
        },
        onInput(e) {
            this.messageSend = e.target.innerText;
            if (this.isUnicode(this.messageSend)) {
                this.dir = "rtl";
            } else {
                this.dir = "ltr";
            }
        },
        dragover(event) {
        event.preventDefault();
        // Add some visual fluff to show the user can drop its files
        event.currentTarget.classList.add("active");
        },
        dragleave(event) {
        // Clean up
        event.currentTarget.classList.remove("active");
        },
        drop(event) {
        event.preventDefault();
        event.currentTarget.classList.remove("active");
        },
        upload() {
            if(document.querySelector(".dropDiv")) {
                document.querySelector(".dropDiv").click();
            }
        },
        getChatContent() {
            if(this.chatIdC !== 0) {
                    
                this.chat = [];
                this.loading = false;
                this.openQuick = false;
                this.emoj = "";
                this.messageSend = "\n";
                this.closeEmoji = true;
                this.messageDates = [];
                this.closeRecorder();
                this.$http
                    .get(this.urlApi+`messages?limit=30&chatId=${this.chatIdC}&page=${this.load}`)
                    .then((res) => {
                    if (res.data.length !== 0) {
                        var msgs = res.data.data.reverse()
                        this.chat = msgs;
                        this.FixDates();
                        
                    }
                    if(this.chat.length < 10) {
                        this.loading = true
                    }
                    this.goDown(500);
                });
                this.$http
                    .get(this.urlApi+`contact?chatId=${this.chatIdC}`)
                    .then((res) => {
                        this.$store.dispatch("contactAction", {contact:res.data.data});
                });

                
            }
        },
        focusInput() {
            if(this.$refs.textContent){
                this.$refs.textContent.focus();
            }
        },
        loadMore() {
            this.load++;
            this.messageDates = [];
            var limitIf = "";
            if(this.limit !== 0) {
                limitIf = `&limit=${this.limit}`;
            }
            if(this.chat) {
                var firstMsg = this.chat[0].id;
            }
            this.$http.get(this.urlApi+`messages?chatId=${this.chatIdC}&page=${this.load}${limitIf}`)
            .then((res) => {
                if (res.data.data.length) {
                    this.chat.unshift(...res.data.data.reverse());
                    this.FixDates();
                    setTimeout(() => {
                        
                        if(this.$refs["vs"]) {
                            // 3770
                            this.$refs["vs"].scrollTo(
                            {
                                y: document.getElementById(firstMsg).offsetTop - 150
                            },1);
                        }
                    },1)
                    
                } else {
                    this.loading = true
                }
                limitIf = "";
            });
        },
        FixDates() {
            if(this.chat !== []) {
                this.chat.forEach((element) => {
                if (element.created_at_day) {
                    if (this.messageDates.includes(element.created_at_day)) {
                    element.isToday = false;
                    } else {
                    element.isToday = true;
                    this.messageDates.push(element.created_at_day);
                    }
                }
                });
            }
        },
        sendMsg(event) {
            if(this.$refs.textContent) {
                
                this.closeEmoji = true;
                setTimeout(() => {
                    this.$refs.textContent.click();
                }, 100);
            }
            var x = window.matchMedia("(min-width: 991px)")
  
            if(x.matches) {
                if (event.keyCode == 13) {
                    if(event.shiftKey){
                        event.stopPropagation();
                    } else {
                        this.sendFunc();
                    }
                }
            }
        },
        sendFunc() {
            if (this.messageSend !== "" || this.$refs.myVueDropzone.dropzone.files.length !== 0) {

                if (this.$refs.myVueDropzone.dropzone.files.length >= 1 && this.$refs.myVueDropzone.dropzone.files.length <= 29) {
                    
                    var x = 0;
                    for (var i in this.$refs.myVueDropzone.dropzone.files) {
                        var data = new FormData();
                       // console.log(this.$refs.myVueDropzone.dropzone.files[i].type)
                        data.append("chatId", this.chatIdC);
                        var type;
                        if(this.$refs.myVueDropzone.dropzone.files[i].type.includes("image")) {
                            data.append("type", "2");
                            type = 'image';
                        } else if(this.$refs.myVueDropzone.dropzone.files[i].type.includes("text") || this.$refs.myVueDropzone.dropzone.files[i].type.includes("sheet") || this.$refs.myVueDropzone.dropzone.files[i].type.includes("pdf")) {
                            data.append("type", "2");
                            type = 'document';
                        } else if(this.$refs.myVueDropzone.dropzone.files[i].type.includes("video")) {
                            data.append("type", "3");
                            type = 'video';
                        } else if(this.$refs.myVueDropzone.dropzone.files[i].type.includes("audio")) {
                            data.append("type", "4");
                            type = 'ptt';
                        }
                        if(this.replyArry !== null ) {
                            data.append("replyOn", this.replyArry.id);
                        }
                      //  console.log(this.$refs.myVueDropzone.dropzone.files[i].type)
                        data.append("file", this.$refs.myVueDropzone.dropzone.files[i]);

                        x++;

                        if(x === 1) {
                            data.append("caption", this.messageSend);
                        }

                        var today1 = new Date();
                        var hours1 = today1.getHours();
                        var seconds1 = today1.getSeconds();
                        var ampm1 = hours1 >= 12 ? 'PM' : 'AM';
                        var time1 = today1.getHours() + ":" + today1.getMinutes() + " " + ampm1;
                        var idRandom1 = "id_"+ Math.floor(Math.random() * 99999) + 1 + Math.floor(Math.random() * 9999) + seconds1;
                        data.append("frontId", idRandom1);
                        var textMsg = this.$refs.myVueDropzone.dropzone.files[i].name;
                        if(type === 'image') {
                             textMsg = this.messageSend;
                        }
                        //this.messageSend
                        var objMsg1 = {
                            author:this.chatIdC,
                            body:this.$refs.myVueDropzone.dropzone.files[i].dataURL,
                            file_name:textMsg,
                            created_at_time:time1,
                            fromMe:1,
                            id:"",
                            quotedMsgObj:this.replyArry,
                            frontId:idRandom1,
                            isForwarded:0,
                            sending_status:0,
                            testMsg:true,
                            metadata:'null',
                            whatsAppMessageType:type
                        }
                        
                        this.chat.push(objMsg1);

                        this.goDown(0);

                       
                        this.$http
                        .post(this.urlApi+`sendMessage`, data).then((res) => {
                            this.chat.forEach((element) => {
                            if (element.frontId !== '' && res.data.data.frontId !== '') {
                                if (element.frontId.search(res.data.data.frontId) !== -1) {
                                    element.author = res.data.data.author;
                                    element.body = res.data.data.body;
                                    element.file_name = res.data.data.file_name;
                                    element.created_at_time = res.data.data.created_at_time;
                                    element.fromMe = res.data.data.fromMe;
                                    element.id = res.data.data.id;
                                    element.quotedMsgObj = res.data.data.quotedMsgObj;
                                    element.frontId = res.data.data.frontId;
                                    element.file_size = res.data.data.file_size;
                                    element.isForwarded = res.data.data.isForwarded;
                                    element.sending_status= res.data.data.sending_status;
                                    element.testMsg = res.data.data.testMsg;
                                    element.status = res.data.data.status;
                                    element.metadata = res.data.data.metadata;
                                    element.whatsAppMessageType = res.data.data.whatsAppMessageType;
                                }
                            }
                            });
                        

                        }).catch((err) => {
                            this.$emit("checkSubscription",err.response.statusText);
                        });

                        
                    } 
                } else {
                    data = new FormData();
                    data.append("chatId", this.chatIdC);
                    data.append("message", this.messageSend);
                    if(this.replyArry !== null ) {
                        data.append("replyOn", this.replyArry.id);
                    }
                    // @ts-ignore
                    data.append("type", 1);
        
                    var today = new Date();
                    var hours = today.getHours();
                    var seconds = today.getSeconds();
                    var ampm = hours >= 12 ? 'PM' : 'AM';
                    var time = today.getHours() + ":" + today.getMinutes() + " " + ampm;
                    var idRandom = "id_"+ Math.floor(Math.random() * 99999) + 1 + Math.floor(Math.random() * 9999) + seconds;

                    data.append("frontId", idRandom);
                    var objMsg = {
                        author:this.chatIdC,
                        body:this.messageSend,
                        caption:"",
                        created_at_time:time,
                        fromMe:1,
                        id:"",
                        quotedMsgObj:this.replyArry,
                        frontId:idRandom,
                        isForwarded:0,
                        sending_status:0,
                        testMsg:true,
                        metadata:'null',
                        whatsAppMessageType:"chat"
                    }
                    
                    this.chat.push(objMsg);
                    this.goDown(0);


                    this.$http
                    .post(this.urlApi+`sendMessage`, data).then((res) => {
                        if(res.data.status.message !== 'Chat ID Is Invalid') {
                            this.chat.forEach((element) => {
                                if (element.frontId) {
                                    
                                    if (element.frontId.search(res.data.data.frontId) !== -1) {
                                        element.author = res.data.data.author;
                                        element.body = res.data.data.body;
                                        element.caption = res.data.data.caption;
                                        element.created_at_time = res.data.data.created_at_time;
                                        element.fromMe = res.data.data.fromMe;
                                        element.id = res.data.data.id;
                                        element.quotedMsgObj = res.data.data.quotedMsgObj;
                                        element.frontId = res.data.data.frontId;
                                        element.isForwarded = res.data.data.isForwarded;
                                        element.sending_status= res.data.data.sending_status;
                                        element.testMsg = res.data.data.testMsg;
                                        element.status = res.data.data.status;
                                        element.whatsAppMessageType = res.data.data.whatsAppMessageType;
                                    }
                                }
                        });
                       }
                        
                    }).catch((err) => {
                        this.$emit("checkSubscription",err.response.statusText);
                    });
                }
                this.messageSend = "";
                var elm = document.getElementsByClassName("post")[0];
                elm.innerHTML = elm.getAttribute("placeholder");
                this.replyArry = null;
            }
            if(this.$refs.myVueDropzone) {
                this.$refs.myVueDropzone.removeAllFiles(true);
            }
        },
        onResultRecord (file) {
            if(this.cancleRec === false) {
          
                
                var data = new FormData();
                data.append("chatId", this.chatIdC);
                data.append("type", 4);
                if(this.replyArry !== null ) {
                    data.append("replyOn", this.replyArry.id);
                }

                var today = new Date();
                var hours = today.getHours();
                var seconds = today.getSeconds();
                var ampm = hours >= 12 ? 'PM' : 'AM';
                var time = today.getHours() + ":" + today.getMinutes() + " " + ampm;
                var idRandom = "id_"+ Math.floor(Math.random() * 99999) + 1 + Math.floor(Math.random() * 9999) + seconds;
                data.append("frontId", idRandom);
                //this.messageSend
                var objMsg = {
                    author:this.chatIdC,
                    body:window.URL.createObjectURL(file),
                    file_name:"",
                    created_at_time:time,
                    fromMe:1,
                    id:"",
                    timeSec:{
                        mins:this.countRec.minutes,
                        secs:this.countRec.seconds
                    },
                    quotedMsgObj:this.replyArry,
                    frontId:idRandom,
                    isForwarded:0,
                    sending_status:0,
                    testMsg:true,
                    metadata:'null',
                    whatsAppMessageType:"ptt"
                }
                
                this.chat.push(objMsg);
                    this.$refs["vs"].scrollTo(
                    {
                        y: "110%"
                    },1);
                data.append("size", file.size);
                data.append("file", file);
              
                this.$http
                .post(this.urlApi+`sendMessage`, data).then((res) => {
                    this.chat.forEach((element) => {
                    if (element.frontId !== '' && res.data.data.frontId !== '') {
                        if (element.frontId.search(res.data.data.frontId) !== -1) {
                            element.author = res.data.data.author;
                            element.body = res.data.data.body;
                            element.file_name = res.data.data.file_name;
                            element.created_at_time = res.data.data.created_at_time;
                            element.fromMe = res.data.data.fromMe;
                            element.id = res.data.data.id;
                            element.quotedMsgObj = res.data.data.quotedMsgObj;
                            element.frontId = res.data.data.frontId;
                            element.file_size = res.data.data.file_size;
                            element.isForwarded = res.data.data.isForwarded;
                            element.sending_status= res.data.data.sending_status;
                            element.testMsg = res.data.data.testMsg;
                            element.status = res.data.data.status;
                            element.metadata = res.data.data.metadata;
                        }
                    }
                    });
                

                }).catch((err) => {
                    this.$emit("checkSubscription",err.response.statusText);
                });

                
            
                this.replyArry = null;
            }
            for (var i = 1; i < 99999; i++) {
                window.clearInterval(i);
            }
            this.countRec.minutes = 0;
            this.countRec.seconds = 0;
            this.countRec.totalSec = 0;
        },
        onStreamRecord (){
            this.cancleRec = false;
            this.closeRec = false;
            setInterval(() => {
                this.countRec.seconds = ++this.countRec.totalSec % 60
                this.countRec.minutes = parseInt(this.countRec.totalSec / 60, 10) % 60
            }, 1000);

        },
        closeRecorder() {
            this.cancleRec = true;
            setTimeout(() => {
                this.closeRec = true;
            },50)
            
        },
        goDown(num) {
            if(this.$refs["vs"]) {
                setTimeout(() => {
                    this.$refs["vs"].scrollTo(
                    {
                        y: "100%"
                    },1);
                }, num);
            }
        },
        getQuick() {
           this.$http.get(this.urlApi+`quickReplies`)
            .then((res) => {
                this.quickReplies = res.data.data
            });
        },
        closeQuick() {
                this.goDown(100); 
            this.openQuick = !this.openQuick
        },
        sendQuick(msg) {
            if(this.messageSend === "/") {
                const inputId = document.getElementById("inputId");
                inputId.textContent = '';
                this.messageSend = "";
            }
            this.openQuick = false;
            this.messageSend = msg;
            //this.sendFunc();
            $("#inputId").text(msg);
            this.focusInput();
        },
        sendReplyBtns(msg) {
            this.messageSend = msg;
            this.sendFunc();
            this.focusInput();
        },
        funcPause(playerId) {
            for (var i in this.$refs.component) {
                if(this.$refs.component[i].playerid !== playerId) {
                   // var sounds = document.getElementsByTagName('audio');
                      this.$refs.component[i].$refs.player.pause()
                    this.$refs.component[i].isPlaying = false
                } 
            }  
            
        },
        openOptions(id) {
            $(".options").not($(".options"+id)).removeClass("active");
            $('.listOptions').not($(".listOptions"+id)).slideUp();
            $('.listOptions'+id).slideToggle();
            $(".options"+id).toggleClass("active");
           
        },
        reply(arry) {
            delete  arry.quotedMsgBody;
            delete  arry.quotedMsgType;
            this.replyArry = arry;
            this.focusInput();
        },
        closeReply() {
            this.replyArry = null;
        },
        replyClick(id) {
            if(document.getElementById(id) !== null) {
               var offsetTop =  document.getElementById(id).offsetTop
                if(this.$refs["vs"]) {
                    this.$refs["vs"].scrollTo(
                    {
                        y: offsetTop - 200
                    },1);
                }
                document.getElementById(id).classList.add("active");
                setTimeout(function() {
                    document.getElementById(id).classList.remove("active");
                },500)
                
            } else {

                this.$http.get(this.urlApi+`messages?chatId=${this.chatIdC}&message_id=${id}`)
                .then((res) => {
                    if (res.data.data.length) {
                        this.chat = res.data.data.reverse();
                        this.load = 1;
                        this.limit = res.data.pagination.count;
                        setTimeout(() => {

                            document.getElementById(id).classList.add("active");
                            setTimeout(function() {
                                document.getElementById(id).classList.remove("active");
                            },500)

                            if(this.$refs["vs"]) {
                                this.$refs["vs"].scrollTo(
                                {
                                    y: document.getElementById(id).offsetTop
                                },1);
                            }
                        },500);

                        /*if(this.$refs["vs"]) {
                            this.$refs["vs"].scrollTo(
                            {
                                y: offsetTop2 - 200
                            },1);
                        }*/
                        
                        
                        
                    }
                })
            }
            
        }
    
    
    },
    components:{
        audioplayer:audioplayer,
        vueDropzone: vue2Dropzone,
        'twemoji-textarea': TwemojiTextarea,
        vuescroll
    }
}

</script>

<style scoped>

.options
{
    position:absolute;
    left:0;
    top:0;
    z-index: 1;
    border-top-left-radius:7px;
    text-align: left;
    height:23px;
    opacity: 0;
    transition:all 0.3s;
    background: linear-gradient(-15deg,rgba(0,0,0,0),
    rgba(0,0,0,0) 45%,
    rgba(0,0,0,.08) 70%,rgb(0 0 0 / 8%))
}

.options .openOptions
{
    font-size:22px;
    color:#000;
    width:30px;
    height:23px;
    text-align: center;
    line-height:24px;
    cursor:pointer;
}

.listOptions
{
    position:absolute;
    top:100%;
    left: 8px;
    border-radius: 3px;
    background-color:#fff;
    padding:10px 0;
    width:160px;
    z-index: 2;
    display:none;
    text-align: right;
    box-shadow: 0 2px 5px 0 rgba(0,0,0,.26),0 2px 10px 0 rgba(0,0,0,.26);
}

.listOptions li
{
    display:block;
    height:45px;
    line-height:45px;
    color:#000;
    padding:0 20px;
    font-size:16px;
    text-align:right!important;
    cursor:pointer
}

.optionsBotStyle
{
    min-width:150px;
}

.optionsBotStyle .boldTitle
{
    font-size:15px;
    color:#000;
    font-family: "Tajawal-Bold";
    margin-bottom:3px;
}

.optionsBotStyle .title
{
    font-size:14px;
    color:#000;
    font-family: "Tajawal-Regular";
    font-weight:normal;
    margin-bottom:3px;
}

.optionsBotStyle .footerMsg
{
    color:rgba(0,0,0,0.45);
}

.listBotBtns
{
    padding:0;
    margin-top:-5px;
}

.listBotBtns li
{
    padding:5px 15px;
    text-align:center!important;
    border-radius:5px;
    display:block;
    background-color:#fff;
    color:#000;
    cursor:pointer;
    margin-bottom:5px;
}

.right .listBotBtns li
{
    background-color:#dcf8c6
}

.listOptions li:hover
{
    background-color:#f5f5f5
}

.replyContainer
{
    padding:10px 30px 0px 50px;
    background-color:#f0f0f0;
    position: relative;
}
.replyContainer .replyMsg
{
    margin:0;
}


.replyContainer .closeReply
{
    position:absolute;
    left:15px;
    top:28px;
    color:#919191;
    opacity: 1;
    cursor:pointer;
}

.replyContainer .replyMsg
{
    background-color:#e4e4e4
}

.chat-conversation .conversation-list .ctext-wrap .ctext-wrap-content .replyMsg .fa-user
{
    font-size:13px;
    margin-top:4px
}

.replyMsg
{
    cursor:pointer;
    padding:10px 15px;
    position:relative;
    margin: -4px -4px 10px;
    min-width: 150px;
    border-radius: 5px;
    overflow:hidden;
    text-align: right;
    background-color:#f0f0f0;
    border-right:none;
}

.right .replyMsg,
.right .contactSend .UserIcon
{
    background-color:#cfe9ba;
}

.right .contactSend .UserIcon
{
   color:#9f9f9f
}

.replyMsg.withImage
{
    padding-left:60px;
    min-width:200px;
    max-width:100%;
}

.replyMsg:before
{
    content:"";
    position: absolute;
    width:5px;
    right:0;
    top:0;
    height:100%;
    background-color:#6bcbef
}

.replyMsg.fromMe:before
{
    background-color:#35cd96
}

.replyMsg .replyName
{
    font-family: "Tajawal-Bold";
    font-size:14px;
    margin-bottom:0px;
    display:block;
    color:#6bcbef;
}

.replyMsg.fromMe .replyName
{
    color:#35cd96
}

.replyMsg .imgrelpy
{
    width:58px!important;
    height:100%;
    position: absolute;
    background-color:#fff;
    left:0;
    top:0;
    cursor:default!important;
    border:2px solid #f0f0f0;
    border-top-left-radius: 5px;
    border-bottom-left-radius:5px;
}

.replyMsg.fromMe .imgrelpy
{
    border-color: #cfe9ba
}

.replyMsg .msg
{
    color:#00000099;
    font-size:13px;
}

.replyMsg .msg i
{
    float:right;
    margin-left:5px;
    font-size:11px;
    margin-top:6px;
    color:#0000004d;
}

.ChatDiv
{
    display: flex;
    flex-direction: column;
    height:100vh;
    position: relative;
    transition:none
}

.chatHead
{
    position: absolute;
    right: 0;
    top: 0;
    width: 100%;
    z-index: 100;
    height:60px;
    padding: 0.6rem 0.8rem!important;
    border-left: 1px solid #00000014;
    background-color:#ededed
}

.user-chat-nav
{
    float:left
}

.user-chat-nav li
{
    float:left;
}

.list-inline-item:not(:last-child)
{
    margin-right:10px;
}

.user-chat-nav .nav-btn
{
    color:#919191!important;
    width:25px;
}

.chat-conversation
{
    flex: 1 1 0;
    margin-top:60px;
    overflow: hidden;
    padding: 0!important;
    height:auto!important;
    background-color:#e5ddd5;
    position: relative;
}

.chat-conversation li
{
    text-align: left;
}


.chat-conversation .media 
{
    direction: ltr;
}

.chat-conversation .bg
{
    position: absolute;
    right:0;
    top:0;
    pointer-events:none ;
    width:100%;
    height:100%;
    background:url(/images/bg-chat-tile-light_04fcacde539c58cca6745483d4858c52.png);
    opacity: 0.06;
}
#listChat
{
    scroll-behavior: smooth;
    padding-left:20px;
    padding-right:20px;
    padding-top:20px;
}

.chat-conversation .conversation-list
{
    float:right;
}

.chat-conversation .right .conversation-list
{
    float:left;
}


.chat-conversation .conversation-list .conversation-name
{
    text-align: left;
    min-height:21px;
}

.chat-conversation .right .conversation-list .ctext-wrap-content
{
    border-radius: 8px 8px 8px 0px!important;
}

.chat-conversation .right .conversation-list .ctext-wrap-content .options
{
    left:auto!important;
    border-top-right-radius:7px;
    border-top-left-radius:0;
    text-align:right;
    right:0;
    background: linear-gradient(
    15deg
    ,rgba(0,0,0,0), rgba(0,0,0,0) -45%, rgba(0,0,0,.08) 70%,rgb(0 0 0 / 8%));
}

.chat-conversation .conversation-list .ctext-wrap-content .listOptions
{
    right:calc(100% - 20px);
    left:auto!important;
}

.chat-conversation .right .conversation-list .ctext-wrap-content .listOptions
{
    left:calc(100% - 20px)!important;
}

.chat-conversation .conversation-list .ctext-wrap-content
{
    background-color:#fff;
    max-width:450px;
    padding:12px 10px 7px;
    border-radius: 8px 8px 0px 8px;
    color:#000;
    word-wrap: break-word;
}

.chat-conversation .conversation-list .videoStyle
{
    width:100%;
    max-width:200px;
}

@media(max-width:767px){
    .chat-conversation .conversation-list .ctext-wrap-content
    {
        max-width:100%;
    }
}

.chat-conversation .conversation-list .ctext-wrap-content:before{
    border-color: #fff transparent transparent #fff;
    border-left-color: transparent;
    border-right-color: #fff;
}

.chat-conversation .conversation-list .ctext-wrap-content img
{
    width:100%;
    display: block;
    margin-bottom: 10px;
    cursor:pointer;
}

.contactSend
{
    padding-right:65px;
    position:relative;
    display:block;
    min-height:55px;
}

.contactSend .UserIcon
{
    position:absolute;
    right:5px;
    top:-5px;
    width:50px;
    height:50px;
    line-height:50px;
    border-radius:50%;
    margin:0;
    background:#f9f9f9;
    color:#c2c2c2;
    font-size:23px;
    margin:0;
    text-align:center;
}

.contactSend .contactName 
{
    font-family: "Tajawal-Bold";
    margin-bottom:3px;
    font-size:14px;
    min-width:160px;
    margin-top:5px;
}


.contactSend .contactPhone
{
    direction:ltr;
    display:block;
    text-align:right
}

.productStyle .head
{
    background-color:#f9f9f9;
    border:2px solid #f9f9f9;
    overflow:hidden;
    position:relative;
    padding:9px 15px 15px 69px;
    direction:ltr;
    text-align:left;
    margin-bottom:10px;
    min-width:240px;
    height:60px;
}

.productStyle2.productStyle .head
{
    padding: 5px  8px 6px;
    height:auto;
}

.productStyle2.productStyle .head .productName
{
    padding-bottom:0;
    font-family: "Tajawal-Bold";
}

.productStyle2.productStyle .head .productPrice
{
    font-family: "Tajawal-Medium";
}

.productStyle .head .productImg
{
    position:absolute;
    left:0;
    top:0;
    width:60px!important;
    height:60px;
    background:#fff;
    padding:10px;
}

.productStyle .head .productQuantity
{
    font-size:13px;
    font-family: "Tajawal-Bold";
    display:block;
    
}

.productStyle .head .productPrice
{
    font-size:12px;
    display:block;
    padding-left:2px;
}

.productStyle .productName
{
    border-bottom:1px solid #f9f9f9;
    padding-bottom:10px;
    text-align:left;
}

.productStyle .productBtn
{
    text-align:center;
    color:#6bcbef;
    padding:5px;
    display:block;
    border-bottom:1px solid #f9f9f9;
    margin-bottom:10px;
}

.right .productStyle .head {
    background-color:#cfe9ba;
}

.right .productStyle .productBtn,
.right .productStyle .productName,
.right .productStyle .head
{
    border-color:#cfe9ba
}

.right .productStyle .productBtn
{
    color:#343a40
}

.chat-conversation .right
{
    direction: ltr;
}

.chat-conversation .right .chat-time
{
    text-align: left;
    margin-bottom:-10px;
    margin-left:-5px;
    margin-top:5px;
}

.chat-conversation .right .chat-time span
{
    margin-left:5px;
    float:left
}

.chat-conversation .right .chat-time span.seen
{
    margin-top:-1px
}

.chat-input-section
{
    position: relative;
    z-index: 100;
    background-color:#fff
}

.mapMessage
{
    display:block;
    max-width:260px;
    color:#039be5
}

.mapIframe
{
    display:block;
    position:relative
}

.mapIframe a
{
    position: absolute;
    right:0;
    top:0;
    width:100%;
    height:100%;
}

.mapIframe iframe
{
    max-width:100%;
}

.sendMessage {
  box-shadow: 0 0 6px 3px rgba(0, 0, 0, 0.04);
  background-color: #f0f0f0;
  display: block;
  padding: 10px 15px 11px;
  display: flex;
  flex-direction: row;
  align-items: flex-end;
  z-index: 100;
  max-width: 100%;
  flex: none;
  order: 3;
}

.sendMessage .textField {
  background-color: #fff;
  padding: 8px 0 8px;
  color: #000;
  border-radius: 30px;
  position: relative;
  flex: 1 1 auto;
  width: 100%;
  cursor: text;
  overflow: hidden;
  z-index: 1;
  margin-right:44px;
}

.sendMessage .textField .textV {
    position: absolute;
    right: 18px;
    top: 8px;
    z-index: -1;
    font-size: 14px;
    color: #999;
}

.sendMessage .textField div {
  z-index: 1;
  max-height: 100px;
  word-wrap: break-word;
  padding: 0 15px 0;
  overflow: hidden;
  min-height: 21px;
  font-size:14px;
    -webkit-transition:all 0.3s;
    -moz-transition:all 0.3s;
    -o-transition:all 0.3s;
    transition:all 0.3s;
}

.sendMessage .textField div[dir="ltr"] {
  direction: ltr;
  text-align: left;
}
.sendMessage .textField div[dir="rtl"] {
  direction: rtl;
  text-align: right;
}

.openQuick
{
  font-size: 25px;
  text-align: center;
  background: none;
  padding: 0;
  border: none;
  flex: none;
  margin-left: 10px;
  height: 37px;
  color:#919191;
-webkit-transition:all 0.3s;
-moz-transition:all 0.3s;
-o-transition:all 0.3s;
transition:all 0.3s;
}

.openQuick.active
{
    color:#1bc5bd;
}

.vue-audio-recorder.block
{
    display:block;
}

.vue-audio-recorder
{
    background: none;
    padding: 0;
    border: none;
    height:37px;
    width:37px;
    display:none;
    line-height:48px;
    padding-left:5px;
    flex: none;
    color:#919191;
    margin-right:0;
    margin-left: -31px;
    border-radius:50%;
    text-align:left;
    -webkit-transition: all 0.3s;
    -moz-transition: all 0.3s;
    -o-transition: all 0.3s;
    transition: all 0.3s;
}

.vue-audio-recorder:after
{
    content:"\f130";
    font: normal normal normal 14px/1 FontAwesome;
    border:none;
    font-size:25px;
    display:inline-block;
    color:#919191;
    position: static;
    background:none;
    width:auto;
    height:auto;
    -webkit-transition: all 0.3s;
    -moz-transition: all 0.3s;
    -o-transition: all 0.3s;
    transition: all 0.3s;
}

.vue-audio-recorder.active:after
{
    border:none;
    color:#fff;
    background:none;
}

.vue-audio-recorder:hover
{
    background:none;
}

.vue-audio-recorder.active,
.vue-audio-recorder.active:hover
{
    margin-right:15px;
    line-height:44px;
    margin-left:0;
    padding-left:0;
    text-align:center;
    background-color: #ef5350;
    -webkit-animation: pulse 1.25s cubic-bezier(.66,0,0,1) infinite;
    animation: pulse 1.25s cubic-bezier(.66,0,0,1) infinite;
}

.vue-audio-recorder.active:after
{
    font-size:20px
}

@keyframes pulse{
    to{   
        -webkit-box-shadow:0 0 0 10px rgba(239,83,80,.1);
        box-shadow:0 0 0 10px rgba(239,83,80,.1);background-color:#e53935;
        -webkit-transform:scale(.9);transform:scale(.9)
    }
}


.closeRecord.block
{
    display:block;
}

.closeRecord
{
    border:2px solid #ef5350;
    min-height:30px;
    display:none;
    min-width:36px;
    text-align:center;
    line-height:24px;
    color:#ef5350;
    background:none;
    border-radius:50px;
    top:50px;
    position:relative;
    opacity:0;
    overflow:hidden;
    padding:0;
    transform:scale(0);
    -webkit-transition: all 0.3s;
    -moz-transition: all 0.3s;
    -o-transition: all 0.3s;
    transition: all 0.3s;
}

.vue-audio-recorder.active ~ .closeRecord
{
    top:-3px;
    opacity:1;
    min-width:30px;
    margin-right:15px;
    transform:scale(1);
}

.counterRec
{
    min-width: 60px;
    text-align: center;
    height: 37px;
    line-height: 43px;
    display:none;
    margin-left:-63px;
    margin-right:0;
    opacity:0;
    visibility:hidden;
    position:relative;
    -webkit-transition: all 0.3s;
    -moz-transition: all 0.3s;
    -o-transition: all 0.3s;
    transition: all 0.3s;
}

.vue-audio-recorder.active ~ .counterRec
{
    opacity:1;
    margin-left:0;
    visibility:visible;
    margin-right:15px;
}


.counterRec.block
{
    display:block
}

.sendMessage .btnSend {
  font-size: 25px;
  text-align: center;
  background: none;
  padding: 0;
  border: none;
  flex: none;
  margin-right: 15px;
  height: 37px;
  transform: scaleX(-1);
  color: #1bc5bd;
}


.sendMessage .btnSend:disabled {
  color: #919191;
}

.sendMessage .uploadFile {
  position: relative;
  cursor: pointer;
  overflow: hidden;
  text-align: center;
  min-width:24px;
  margin-bottom: 6px;
  color: #919191;
  padding-left:0;
}

.sendMessage .uploadFile input {
  position: absolute;
  right: -999px;
}

.goDown {
  position: absolute;
  left: 20px;
  bottom: -100%;
  width: 45px;
  height: 45px;
  border-radius: 50%;
  text-align: center;
  font-size: 25px;
  color: #000;
  cursor: pointer;
  box-shadow: 0 6px 8px -3px rgba(0, 0, 0, 0.2);
  z-index: 99;
  line-height: 47px;
  background: #fff;
  -webkit-transition: all 0.3s;
  -moz-transition: all 0.3s;
  -o-transition: all 0.3s;
  transition: all 0.3s;
}

.goDown span
{
  font-size: 11px;
  background-color: #c9f7f5;
  color: #000;
  position: absolute;
  right: -5px;
  box-shadow: 0 2px 4px 1px rgba(0, 0, 0, 0.1);
  top: -5px;
  border-radius: 50%;
  width: 23px;
  height: 23px;
  line-height: 23px;
  font-family: "Tajawal-Bold";
  text-align: center;
}


.goDown.active {
  bottom: 30px;
}

.user-chat
{
    box-shadow:none!important
}

.user-chat-remove
{
    font-size: 31px!important;
    display: inline;
    padding: 0!important;
}
.chat-conversation  .conversation-list,
.chat-conversation  .conversation-list .user-chat-content,
.chat-conversation  .conversation-list .ctext-wrap
{
    max-width:100%;
}

.chat-conversation .right .conversation-list .ctext-wrap
{
    justify-content: flex-start;
}

.chat-conversation .conversation-list .ctext-wrap
{
    position:relative
}

.chat-conversation .ctext-wrap:before
{
    content:"";
    position:absolute;
    top:0;
    left:0;
    background-color:rgba(0,0,0,0.09);
    width:100%;
    height:100%;
    z-index:5;
    border-radius:10px;
    border-bottom-left-radius:0;
    opacity:0;
    visibility:hidden;
  -webkit-transition: all 0.3s;
  -moz-transition: all 0.3s;
  -o-transition: all 0.3s;
  transition: all 0.3s;
}

.chat-conversation .active .ctext-wrap:before
{
    opacity:1;
    visibility:visible;
}

.chat-conversation .conversation-list .ctext-wrap .ctext-wrap-content
{
    
    min-width:100px;
    text-align: right;
    white-space:unset!important
}

.chat-conversation .conversation-list .ctext-wrap .ctext-wrap-content p p
{
    white-space: pre-wrap;
}

.chat-conversation .conversation-list .ctext-wrap .ctext-wrap-content:before
{
    display:none;
}

.chat-conversation .conversation-list .ctext-wrap .ctext-wrap-content.quoted{
    min-width:160px;
}


.chat-conversation .conversation-list .ctext-wrap .ctext-wrap-content:hover .options,
.options.active
{
    left:0;
    opacity: 1;
}

.chat-conversation  .conversation-list .ctext-wrap .ctext-wrap-content [dir="ltr"] {
    text-align: left;
}

.chat-conversation .right .conversation-list .ctext-wrap .ctext-wrap-content
{
    background-color:#dcf8c6;
}

.chat-conversation .right .conversation-list .ctext-wrap .ctext-wrap-content:before
{
    border-left-color:#dcf8c6;
    border-top-color: #dcf8c6;
    
}

.chat-conversation .conversation-list .ctext-wrap .ctext-wrap-content .fa-ban
{
    margin-top:4px;
    margin-left:5px;
    float:right;
}

.chat-conversation .conversation-list .ctext-wrap .ctext-wrap-content .fa-user
{
    float:right;
    margin-left:5px;
    margin-top:2px;
}

.avatar-xs {
    cursor:pointer!important;
    height: 2.2rem!important;
    width: 2.2rem!important;
}

.chat-conversation .conversation-list .chat-time
{
    color:rgba(0,0,0,.5);
    direction: ltr;
}

.chat-leftsidebar
{
    border-left: 1px solid #00000014;
    margin-left:0!important
}

.quickMsgs
{
    background-color:#fff;
    border-top: 2px solid #f5f5f5;
    height:222px;
    overflow:auto;
    position: relative;
}

.quickMsgs.mobile
{
    display:none;
}


@media(max-width:767px) {
    .quickMsgs
    {
        display:none;
    }
    .quickMsgs.mobile
    {
        display:block
    }
}

.quickMsgs .fa-close
{
    font-size:14px;
    color:#000;
    position: absolute;
    left:15px;
    top:15px;
    cursor:pointer;
    z-index: 1;
}

.quickMsgs ul
{
    padding:10px 20px;
    padding-right:15px;
}

.quickMsgs li
{
    height:45px;
    line-height:45px;
    cursor:pointer;
    font-size:13px;
    border-bottom:1px solid #f5f5f5;
}

.quickMsgs li span
{
    font-family: "Tajawal-Bold";
    font-size:13px;
}

.quickMsgs .addRep 
{
    margin-right:15px;
    margin-bottom:15px;
    color:#000;
    font-size:14px;
    display:inline-block
}

.quickMsgs .addRep i
{
    float:right;
    margin-left:5px;
    color:#00c5bb;
    font-size:17px;
    margin-top:-2px
}

.media-body
{
    margin-top:0
}

.conversation-list .media-body
{
    width:190px;
    margin-top:0;
}

.conversation-list .bg-soft-primary
{
    background-color:rgb(239 239 239 / 50%)!important
}

@media (min-width:991px) {
    #listChat
    {
        padding-left:30px!important;
        padding-right:30px!important;
    }

    .conversation-list .media-body {
            width: 175px;
    }
}

@media (max-width: 991px) {
   .chatHead
    {
        position: fixed!important;
    }

  .chatMessages .message .messageContent {
    max-width: 95%;
  }

    .openQuick
    {
        margin-left:10px;
    }
    .openQuick.hidMob
    {
        margin-right:-30px;
        margin-left:0;
        opacity: 0;
        visibility: hidden;
        -webkit-transition:all 0.3s;
        -moz-transition:all 0.3s;
        -o-transition:all 0.3s;
        transition:all 0.3s;
    }
    
    .sendMessage .textField
    {
        margin-right:10px;
    }
    .user-chat-remove
    {
        line-height:36px!important;
    }
}

@media (height:812px) and (width: 375px) 
{
    .sendMessage
    {
        padding-top:15px;
        padding-bottom:35px;
    }

    #popper-button button
    {
        bottom: 25.5px!important;
    }
}

@media (height:375px) and (width: 812px) 
{
    .sendMessage
    {
        padding-top:15px;
        padding-bottom:35px;
    }

    #popper-button button
    {
        bottom: 25.5px!important;
    }
}

@media (max-width:767px) {
    .chat-conversation .conversation-list .ctext-wrap .ctext-wrap-content
    {
        max-width:100%;
    }

    .user-chat-nav
    {
        position: relative;
        top: 2px;
    }

    .user-chat-nav .nav-btn
    {
        line-height:41px!important;
    }
    
}

.conversation-list .image-viewer-wrap .image-div .image-photos:hover>.bottomTitle
{
    display:none;
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


</style>