<template>
        <div class="newMsg">
            <div class="head clearfix">
                <h4 class="title">دردشة جديدة</h4>
                <span @click="closeNew();multiNumber = false; singleNumber = true;numbers = [];" class="back">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"><path fill="currentColor" d="M12 4l1.4 1.4L7.8 11H20v2H7.8l5.6 5.6L12 20l-8-8 8-8z"></path></svg>
                </span>
            </div>
            <form @submit.prevent class="body">
                <ul class="SingleOrMul clearfix">
                    <li @click="multiNumber = false; singleNumber = true;numbers = [];" :class="singleNumber ? 'active' : ''">رسالة فردية</li>
                    <li @click="multiNumber = true; singleNumber = false;newPhone.Mobile = '';newPhone.CountryCode = null;" :class="multiNumber ? 'active' : ''">رسالة جماعية</li>
                </ul>
                
                <div class="relativeInput">
                    <!--<vue-tel-input
                    v-model="newPhone.Mobile"
                    :custom-validate="/^[0-9]*$/"
                    v-on:country-changed="countryChanged"
                    v-bind="bindProps"
                    ></vue-tel-input>
                    
                    <input-tag @input="inputFun" v-model="numbers" :limit="100" :add-tag-on-keys="[32,13]"></input-tag>

                    -->

                        <template v-if="singleNumber === true">
                            <span class="titleInput">اكتب رقم الهاتف :</span>
                            <vue-tel-input
                            v-model="newPhone.Mobile"
                            :custom-validate="/^[0-9]*$/"
                            v-on:country-changed="countryChanged"
                            v-bind="bindProps"
                            ></vue-tel-input>
                        </template>

                        <template v-if="multiNumber === true">
                        
                            <span class="titleInput">اضف الارقام :</span>
                            <label class="inputTags clearfix">
                                <span class="textPlaceHolder" v-if="!numbers[0] && inputNumber === '' ">
                                يمكنك لصق الارقام والفصل بينهم بمسافه أو سطر جديد ويجب كتابة الرقم بالمفتاح الدولي مثال ( 9665xxxxxxxx )
                                </span>
                                <span class="listNumbs text-truncate" v-for="(numb,ind) in numbers"  :key="ind"> {{numb}} <i class="fa fa-close" @click="numbers.splice(ind,1)"></i></span>
                                <input type="text"
                                v-model="inputNumber"
                                @blur="handleBlur"
                                @focus.13="closeEmj" 
                                @keypress.13="closeEmj" 
                                @keydown.13="closeEmj"
                                @keydown.8="removeNum"
                                @keyup="inputFun" />
                            </label>
                        </template>



                    <transition name="fade">
                        <div v-if="multiNumber === true">
                            <div v-if="showMsgErr === true" class="alert alertNumbs alert-danger">
                                <i class="fa fa-close close" @click="showMsgErr = false;numbersErr = []"></i>
                                لم يتم الارسال ل {{ numbersErr.length }} ارقام
                            <!--  يوجد ارقام هاتف غير صحيح :                                
                                <ul>
                                    <li v-for="(numbErr,ind) in numbersErr" :key="ind">{{ ind + 1}} - {{numbErr}}</li>
                                </ul>-->
                            </div>
                        </div>
                        <div v-if="singleNumber === true">
                            <div v-if="showMsgErr === true" class="alert alertNumbs alert-danger text-center">
                                رقم الهاتف غير صحيح
                            </div>
                        </div>
                    </transition>
                    <twemoji-textarea 
                        id="textArMsg"
                        :emojiData="emojiDataAll" 
                        :content.sync="textSend"
                        :placeholder="'كتابة رسالة'"
                        :emojiGroups="emojiGroups" >
                            <template v-slot:twemoji-picker-button>
                                <button class="btnEmoj" @click="clickEm">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"><path fill="currentColor" d="M9.153 11.603c.795 0 1.439-.879 1.439-1.962s-.644-1.962-1.439-1.962-1.439.879-1.439 1.962.644 1.962 1.439 1.962zm-3.204 1.362c-.026-.307-.131 5.218 6.063 5.551 6.066-.25 6.066-5.551 6.066-5.551-6.078 1.416-12.129 0-12.129 0zm11.363 1.108s-.669 1.959-5.051 1.959c-3.505 0-5.388-1.164-5.607-1.959 0 0 5.912 1.055 10.658 0zM11.804 1.011C5.609 1.011.978 6.033.978 12.228s4.826 10.761 11.021 10.761S23.02 18.423 23.02 12.228c.001-6.195-5.021-11.217-11.216-11.217zM12 21.354c-5.273 0-9.381-3.886-9.381-9.159s3.942-9.548 9.215-9.548 9.548 4.275 9.548 9.548c-.001 5.272-4.109 9.159-9.382 9.159zm3.108-9.751c.795 0 1.439-.879 1.439-1.962s-.644-1.962-1.439-1.962-1.439.879-1.439 1.962.644 1.962 1.439 1.962z"></path></svg>
                                </button>
                            </template>
                    </twemoji-textarea>

                </div>
                <button class="send" @click="sendNewMsg()" :disabled="textSend === '' || numbers === []">ارسال</button>
            </form>
            
        </div>
</template>
<script>
import $ from 'jquery';

import {TwemojiTextarea} from '@kevinfaguiar/vue-twemoji-picker';
import EmojiAllData from '@kevinfaguiar/vue-twemoji-picker/emoji-data/en/emoji-all-groups.json';
import EmojiGroups from '@kevinfaguiar/vue-twemoji-picker/emoji-data/emoji-groups.json';


export default {
    name:"newmsg",
    props:['openNew'],
    data() {
        return {
            newPhone: {
                Mobile: "",
                CountryCode: null
            },
            bindProps: {
                mode: "national",
                input: "number",
                placeholder: "رقم الجوال",
                inputClasses: "numbInput",
                dynamicPlaceholder: true,
                inputOptions: null,
            },
            textSend:"",
            showMsgErr:false,
            numbers:[],
            numbersErr:[],
            inputNumber:"",
            multiNumber:false,
            singleNumber:true
        }
    },
    watch:{
        textSend:{
            deep:true,
            handler() {
                if(this.textSend == "<br>") {
                    this.textSend = "";
                    $("#textArMsg").attr("content","");
                     $("#twemoji-textarea").empty();
                }
            }
        },
        inputNumber:{
            deep: true,
            handler() {

                if(this.inputNumber.charAt(0) == " ")
                {
                    this.inputNumber =  this.inputNumber.slice(0, -1)
                }
                this.inputNumber = this.inputNumber.replace(/[^0-9 ]/g, '');

            }
        },
        numbers:{
            deep: true,
            handler() {

                for(var num in this.numbers) {
                    if(this.numbers[num] === "") {
                        this.numbers.splice(num,1);
                    }
                }
            }
        },
    },
    mounted () {
       
    },
    methods:{
        closeEmj(event) {
            if(event.keyCode == 13) {
                 $(".newMsg #popper-container").css("z-index","-2");
                $(".newMsg #popper-container").removeAttr("data-show");
            }
        },
        clickEm() {
            $(".newMsg #popper-container").css("z-index","999");
        },
        handleBlur(){
            if(this.inputNumber !== "" && this.inputNumber.charAt(0) !== " ") {
                var text = this.inputNumber;
                var result = text.split(" ");
                for(var res in result) {
                    this.numbers.push(result[res]);  
                }
                
                this.inputNumber = "";
            }
        },
        countryChanged(country) {
        this.newPhone.CountryCode = country.dialCode;
        },
        closeNew() {
            this.$emit("closeNew");
        },
        sendNewMsg() {
            if(this.multiNumber === true) {
                if(this.textSend !== '' && this.numbers !== []) {
                    this.showMsgErr = false;
                    this.numbersErr = [];
                    //data.append('CountryCode', this.newPhone.CountryCode);
                    //var resArr = [];
                    var numbs = "";
                    for (var numb = 0; numb < this.numbers.length;numb++) {
                        numbs += this.numbers[numb].replace(/^0+/, '') +"@c.us,"
                     }
                    var removeLastChar = numbs.substring(0, numbs.length - 1);
                        //this.numbers.splice(numb, 1);
                        var data = new FormData();
                        data.append('chatId', removeLastChar);
                        data.append('type', 1);
                        data.append('messageType', "new");
                        data.append('message', this.textSend);
                        /*data.append('MessageType', "text");
                        data.append('CountryCode', "");
                        data.append('MessageContent', this.textSend);
                        data.append('phone', this.numbers[numb]);*/
                    
                        this.$http.post(this.urlApi+`sendMessage`,data).then(() => {
                            /*resArr.push(res.data.Code);
                            if(res.data.Code === "0011" || res.data.Code === "0002") {
                                this.showMsgErr = true;
                                this.numbersErr.push(data.get('phone'));
                            }
                            if(numb === this.numbers.length) {
                                if(resArr.includes("0011") || resArr.includes("0002")) {
                                    this.openNew = true;
                                } else {
                                    this.openNew = false;
                                }
                            }*/
                        });
                    
                        
                   
                    this.textSend = "";
                    $("#twemoji-textarea").empty();
                    this.numbers = [];
                    this.closeNew();
                    /*this.numbers = [];
                    this.textSend = "";
                    $("#twemoji-textarea").empty();*/
                    
                } 
            }
            if(this.singleNumber === true) {
                if(this.textSend !== '' && this.newPhone.Mobile !== "") {
                    this.showMsgErr = false;
                    this.numbersErr = [];
                    //data.append('CountryCode', this.newPhone.CountryCode);
                        //this.numbers.splice(numb, 1);
                    var dataS = new FormData();
                    var number = this.newPhone.CountryCode + this.newPhone.Mobile.replace(/^0+/, '') + "@c.us";
                        
                        dataS.append('chatId', number);
                        dataS.append('type', 1);
                        dataS.append('messageType', "new");
                        dataS.append('message', this.textSend);
                    
                        this.$http.post(this.urlApi+`sendMessage`,dataS).then(() => {
                            
                            /*console.log(res)
                            if(res.data.Code === 400) {
                                this.showMsgErr = true;
                                setTimeout(() => {
                                   this.showMsgErr = false; 
                                }, 3000);

                            } else {
                                this.openNew = false;
                            }*/
                        });
                    this.textSend = "";
                    this.newPhone.Mobile = "";
                    this.closeNew();
                    $("#twemoji-textarea").empty();
                    
                } 
            }
        },
        inputFun(event) {

            this.closeEmj(event);

                
            if (event.keyCode === 13 || event.keyCode === 32 || event.keyCode === 9 || event.keyCode == 17 && event.keyCode == 86) {
                this.handleBlur();
            }
            
            

        },
        removeNum() {
            if(this.inputNumber === "") {
                this.numbers.pop();
            }
        }
    },
    computed:{
        emojiDataAll() {
          return EmojiAllData;
        },
        emojiGroups() {
            return EmojiGroups;
        },
        urlApi() {
            return this.$store.getters.urlApi;
        }
    },
    components:{
        'twemoji-textarea': TwemojiTextarea
    }
}
</script>
<style>
    
</style>