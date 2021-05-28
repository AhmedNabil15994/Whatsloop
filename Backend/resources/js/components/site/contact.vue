<template>
    <div class="contactDiv">
        <div class="bgContact" @click="opencontct()"></div>
        <div class="content">
            <div class="head media align-items-center">
                <i class="iconHead" @click="opencontct()">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"><path fill="currentColor" d="M19.1 17.2l-5.3-5.3 5.3-5.3-1.8-1.8-5.3 5.4-5.3-5.3-1.8 1.7 5.3 5.3-5.3 5.3L6.7 19l5.3-5.3 5.3 5.3 1.8-1.8z"></path></svg>
                </i>
                <h2 class="title">معلومات جهة الاتصال</h2>
            </div>
            <vuescroll>
              <div class="contactHead">
                  
                
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
                  <img
                  @click="imageViewer(contact.image)"
                  onerror="this.onerror=null;this.src='https://whatsloop.net/resources/Gallery/UserDefault.png';"
                  :src="
                      contact.image
                      ? contact.image
                      : 'https://whatsloop.net/resources/Gallery/UserDefault.png'
                  "
                  />
                  <h2 class="name">{{ contact.name }}</h2>
              </div>
              
              <div class="contactBody">
                    <div class="row checkStyle" :class="{'active' : checkActive === true}">
                      <label for="#check" class="col-6 title col-form-label font-size-lg">تثبيت المحادثة</label>
                      <div class="col-6 text-left">

                      <div id="check" @click="checktest()" class="switch" :class="{'active' : contact.is_pinned === 1}">
                            <span class="slider round"></span>
                        </div>
                      </div>
                    </div>
                    <form @submit.prevent>
                      <div class="desc">
                        <textarea :value="contact.contact_details.notes" placeholder="ملاحظات العميل" @blur="updateContact('notes',$event.target.value)"></textarea>
                      </div>
                      <div class="contactDetails">
                      <!--<i class="fa edit " :class="editDetails ? 'fa-close' : 'fa-edit'" @click="editDetails = !editDetails"></i>-->
                          <h2 class="title" @click="infoUser = !infoUser"><i class="fa fa-user"></i> بيانات العميل</h2>
                          <ul class="details" v-if="infoUser">
                            <li>الاسم 
                            <input type="text" :value="contact.name" @blur="updateContact('name',$event.target.value)" />
                            </li>
                            <li>رقم الجوال 
                            <input type="text" :value="contact.contact_details.phone" class="numb" disabled/>
                            </li>
                            <li>البريد الالكتروني 
                            <input type="text" :value="contact.contact_details.email" @blur="updateContact('email',$event.target.value)"/>
                            </li>
                            <li>الدولة 
                            <input type="text" :value="contact.contact_details.country" @blur="updateContact('country',$event.target.value)"/>
                            </li>
                            <li>المدينة 
                              <input type="text" :value="contact.contact_details.city" @blur="updateContact('city',$event.target.value)"/>
                            </li>
                            <li>اللغة 
                            <!--<input type="text" :value="contact.contact_details.langText" @blur="updateContact('lang',$event.target.value)"/>-->
                            <div class="selectStyle">
                            <i class="fa fa-angle-down"></i>
                              <select  @change="updateContact('lang',$event.target.value)">
                                <option value="0" selected>Arabic</option>
                                <option value="1" :selected="contact.contact_details.lang === 1">English</option>
                              </select>
                            </div>
                            </li>
                            <li class="supervisor" v-if="supervisors.length > 0">تخصيص مشرف 
                              <br>
                              <div class="clearfix">
                                <span v-for="(sVal,index) in supervisorValue" :key="index" class="mat-success multiselect__tag">
                                  <span>{{ sVal.name }}</span> 
                                  <i aria-hidden="true" @click="removeSuper(sVal.id)"  class="multiselect__tag-icon"></i>
                                </span>
                              </div>
                              <multiselect v-if="supervisorValue.length !== supervisors.length"
                              placeholder="أضف المشرف"
                              tag-placeholder=""
                              deselectLabel="x"
                              selectLabel="اضغط للاضافة"
                              selectedLabel="x"
                              @select="plus = true"
                              v-model="supervisorValue" 
                              :multiple="true" 
                              track-by="id"
                              :taggable="true" label="name" :options="supervisors">
                              </multiselect>
                            
                            
                            </li>
                            <li v-if="options.length > 0">التصنيفات 
                              <br>
                              <div class="clearfix">
                                <span v-for="(val,index) in value" :key="index" :class="val.labelClass"  class="multiselect__tag">
                                  <span>{{ val.name_ar }}</span> 
                                  <i aria-hidden="true" @click="removeLabel(val.color_id,val.labelId)" tabindex="1" class="multiselect__tag-icon"></i>
                                </span>
                              </div>
                              <multiselect v-if="value.length !== options.length"
                              placeholder="حدد اختيارك"
                              tag-placeholder=""
                              deselectLabel="x"
                              selectLabel="اضغط للاضافة"
                              selectedLabel="x"
                              @select="plus = true"
                              v-model="value" 
                              :multiple="true" 
                              track-by="color_id"
                              :taggable="true" label="name_ar" :options="options">
                              </multiselect>
                            
                            
                            </li>
                          </ul>
                          
                      </div>
                    </form>
              </div>
           </vuescroll>
        </div>

    </div>
</template>
<script>
import vuescroll from 'vuescroll';
import Multiselect from 'vue-multiselect'

export default {
  name:"contact",
    data(){
      return {
        imageViewerFlag: false,
        image:[],
        value: [],
        supervisorValue:[],
        infoUser:true,
        checkActive:false,
        plus:false
      }
    },
    props:{
      "contact":{required:true},
      "openContact":{},
      "changename":{},
      "options":{},
      "supervisors":{}
    },
    mounted() {
        if(this.contact.contact_details.labels) {
          this.getLabels();
        }
    },
    watch: {
      contact:{
        handler() {
            this.getLabels();
        },
        deep:true
      },
      value:{
        handler() {
            if(this.plus === true) {
              var str = "";
              
                for (var i in this.value) {
                    str = str + this.value[i].labelId + ",";
                }   
                var removeLastChar = str.substring(0, str.length - 1);
                var usingSplit = removeLastChar.split(',');
                if(usingSplit.length > 0) {
                    this.addLabel(usingSplit[usingSplit.length - 1].toString());
                }
                this.plus = false;
            }
              
        },
        deep:true
      },
      supervisorValue:{
        handler() {
            if(this.plus === true) {
              var str = "";
              
                for (var i in this.supervisorValue) {
                    str = str + this.supervisorValue[i].id + ",";
                }   
                var removeLastChar = str.substring(0, str.length - 1);
                var usingSplit = removeLastChar.split(',');
                if(usingSplit.length > 0) {
                    this.addSuper(usingSplit[usingSplit.length - 1].toString());
                }
                this.plus = false;
            }
              
        },
        deep:true
      }
    },
    methods: {
      checktest() {
        this.checkActive = true;
        setTimeout(() => this.checkActive = false, 500);

       if(this.contact.is_pinned === 0) {
         this.contact.is_pinned = 1;
         this.pinned("pinChat");
        } else {
          this.contact.is_pinned = 0;
          this.pinned("unpinChat");
        }
      },
      opencontct() {
        this.$emit("opencontct");
      },
      imageViewer(image) {
        this.image = [];
        this.image.push(image);
        this.imageViewerFlag = image;
      },
      updateContact(name,value) {
          var data = new FormData();
          data.append('chatId',this.contact.id);

          data.append(name, value);
          this.$http.post(this.urlApi+`updateContact`,data).then(() => {
              /*if(name === "Name_ar") {
                  this.$emit("input",value);
                }*/
          });
      },
      pinned(pinned) {
          var data = new FormData();
          data.append("chatId", this.contact.id);
          this.$http
          .post(this.urlApi+`${pinned}`, data);
      },
      getLabels() {
          this.value = [];
          if(this.options) {
            for (var i in this.options) {
              for(var getL in this.contact.contact_details.labels) {
                if(this.options[i].id === this.contact.contact_details.labels[getL].id ) {
                  this.value.push(this.options[i]);
                }
              }
            }  
          }
          this.supervisorValue = [];
          if(this.supervisors) {
            for (var s in this.supervisors) {
              for(var getS in this.contact.modsArr) {
                if(this.supervisors[s].id.toString() === this.contact.modsArr[getS] ) {
                  this.supervisorValue.push(this.supervisors[s]);
                }
              }
            }  
          }
      },
      removeLabel(colorId,labelId) {
          for (var v in this.value) {
                if(this.value[v].color_id === colorId) {
                    this.value.splice(v, 1);
                    break;
                }  
            } 
          var data = new FormData();
          data.append('chatId',this.contact.id);
          data.append('labelId', labelId);
          this.$http.post(this.urlApi+`unlabelChat`,data).then(() => {
          });
            
      },
      addLabel(id) {
          var data = new FormData();
          data.append('chatId',this.contact.id);
          data.append('labelId', id);
          this.$http.post(this.urlApi+`labelChat`,data).then(() => {
          });
            
      },
      removeSuper(id) {
          for (var v in this.supervisorValue) {
                if(this.supervisorValue[v].id === id) {
                    this.supervisorValue.splice(v, 1);
                    break;
                }  
            } 
          var data = new FormData();
          data.append('chatId',this.contact.id);
          data.append('modId', id);
          this.$http.post(this.urlApi+`removeMod`,data).then(() => {
          });
            
      },
      addSuper(id) {
          var data = new FormData();
          data.append('chatId',this.contact.id);
          data.append('modId', id);
          this.$http.post(this.urlApi+`assignMod`,data).then(() => {
          });
            
      }
    },
    components:{
      vuescroll,
      Multiselect 
    },
    computed:{
      urlApi() {
          return this.$store.getters.urlApi;
      }
    }
}
</script>

<style scoped>

.checkStyle
{
  margin-bottom:10px;
  position:relative
}

.checkStyle .title
{
  color: #7a7f9a;
}

.switch {
  position: relative;
  display: inline-block;
  width: 45px;
  height: 24px;
  margin-top:6px;
  margin-left:15px;
}

.checkStyle.active:before
{
content:"";
position:absolute;
left:0;
top:0;
width:150px;
height:100%;
z-index:2;
cursor:default
}

.switch input { 
  opacity: 0;
  width: 0;
  height: 0;
}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  -webkit-transition: .4s;
  transition: .4s;
}

.slider:before {
  position: absolute;
  content: "";
  height: 16px;
  width: 16px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  -webkit-transition: .4s;
  transition: .4s;
}

.switch.active .slider {
  background-color: #00c5bb;
}

.switch.active .slider:before {
  -webkit-transform: translateX(20px);
  -ms-transform: translateX(20px);
  transform: translateX(20px);
}

/* Rounded sliders */
.slider.round {
  border-radius: 34px;
}

.slider.round:before {
  border-radius: 50%;
}

.bgContact
{
  background-color:rgba(0,0,0,0.4);
  position: absolute;
  right:0;
  top:0;
  width:100%;
  height:100%;
  z-index: 140;
  opacity: 0;
  visibility: hidden;
  cursor:pointer
}
.contactDiv
{
  height:100vh;
  position: relative;
}

.content
{
  background-color:#ededed;
  width: 350px;
  height:100vh;
  max-width: 100%;
  position: relative;
}

@media (max-width: 1200px) {
  .contactDiv
  {
    position: absolute;
    right:0;
    top:0;
    width:100%;
    border:1px solid #e5e5e5;
    z-index: 150;
    overflow: hidden;
  }
  .content
  {
    z-index: 145;
  }

  .bgContact
  {
    opacity: 1;
    visibility: visible;
  }
}

.content .head
{
  background-color:#ededed;
  height:60px;
  position: relative;
  padding-right:70px;
}

.content .head .iconHead
{
  position: absolute;
  right:20px;
  top:11px;
  color:#919191
}

.content .head .title
{
  font-size:15px;
  font-family: "Tajawal-Regular";
  color:#000;
  font-weight:normal
}

.contactDiv .contactHead
{
    padding:20px 20px 25px;
    text-align:center;
    background-color:#fff;
    box-shadow: 0 1px 3px #00000014;
    margin-bottom:7px;
}

.contactDiv .contactHead img
{
    width:130px;
    height:130px;
    border-radius:50%;
    border:4px solid #f5f5f5;
    margin-bottom:20px;
    cursor:pointer
}

.contactDiv .contactHead .name
{
    font-size:17px;
    color:#000;
    font-weight:normal;
    font-family: "Tajawal-Bold";
}

.contactDiv .desc
{
  max-width: 350px;
  margin: 5px auto 0;
  display: block;
  color:#7a7f9a;
  margin-bottom:15px;
}

.iconHead
{
    position:absolute;
    left:20px;
    top:20px;
    font-size:25px;
    cursor:pointer;
    color:#000
}

.contactBody
{
  padding:20px;
  background-color:#fff;
  box-shadow: 0 1px 3px #00000014;
  margin-bottom:7px;
  
}

.contactBody .contactDetails
{
  border:1px solid #f5f5f5;
  border-radius: 5px;
  overflow:hidden;
  margin-bottom: 50px;
}

.contactBody .contactDetails .title
{
  background-color:#fbfbfb;
  padding:13px 15px 15px;
  font-size:15px;
  cursor:pointer;
  font-family: "Tajawal-Bold";
  font-weight:normal;
  position: relative;
}

.contactBody .contactDetails .title i.edit
{
  position: absolute;
  left:15px;
  top:15px;
  font-size:17px;
  color:#000;
  cursor: pointer;
  margin:0
}

.contactBody .contactDetails .title i
{
  font-size:15px;
  margin-left:7px;
  float: right;
  margin-top: 2px;
}

.contactBody .contactDetails .details
{
  margin-top:15px;
  padding-bottom:20px;
  border-bottom:1px solid #f5f5f5;
  border-bottom-right-radius: 5px;
  border-bottom-left-radius: 5px;
}

.contactBody .contactDetails .details li
{
  padding:0 15px;
  color:#7a7f9a
}

.contactBody .contactDetails .details li:not(:last-of-type)
{
  margin-bottom:10px;
}

.contactBody .contactDetails .details li .text
{
  display:block;
  font-family: "Tajawal-Bold";
  color:#000;
  margin-top:5px;
}

.contactBody .contactDetails .details li .tag
{
  display:inline-block;
  padding:3px 10px;
  border-radius: 5px;
  color:#fff;
  margin-top:5px;
  margin-left:5px;
  font-size:14px;
}

.contactBody .contactDetails .details li .tag.color1
{
  background-color:#06d6a0
}

.contactBody .contactDetails .details li .tag.color2
{
  background-color:#00c5bb
}

.contactBody .contactDetails .details li .tag.color3
{
  background-color:#ffd166
}

.contactBody .contactDetails .details input,
.contactBody  textarea,
.contactBody .contactDetails .details .selectStyle select
{
  display:block;
  margin-top:5px;
  width:100%;
  height:40px;
  direction: ltr;
  text-align: right;
  background-color:#f3f6f9;
  padding:0 15px;
  border:none;
  border-radius: 5px;
}

.contactBody .contactDetails .details .selectStyle
{
  position:relative;
  z-index: 1;
  background-color:#f3f6f9;
}

.contactBody .contactDetails .details .selectStyle select
{
  direction: rtl;
  cursor:pointer;
  -moz-appearance:none; /* Firefox */
  -webkit-appearance:none; /* Safari and Chrome */
  appearance:none;
}

.contactBody .contactDetails .details .selectStyle i
{
  position:absolute;
  left:10px;
  top:10px;
  font-size:20px;
  color:#000
}

.contactBody .contactDetails .details .selectStyle select
{
  background:none;
}

.contactBody .contactDetails .details input.numb
{
  direction: ltr;
  text-align: right;
}

.contactBody  textarea
{
  resize:none;
  height:100px;
  padding:15px;
}

.padding
{
  padding:0 15px;
}

.btnSave
{
  display:block;
  width:100%;
  font-family: "Tajawal-Bold";
  height:45px;
  line-height:45px;
  max-width:250px;
  margin:15px auto 0;
  padding:0;
  border-radius: 30px;
}

.list-inline-item:not(:last-child)
{
  margin-left:0;
}


@media (max-width:767px) {
  .multiselect__tag-icon, .multiselect__tag-icon:hover
  {
    line-height:17px;
  }

  .content
  {
    width:100%;
  }
}

</style>