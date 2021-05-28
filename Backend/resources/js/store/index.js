import Vue from "vue";
import Vuex from "vuex";

Vue.use(Vuex);

export const store = new Vuex.Store({
  state: {
    lang: "ar",
    error: null,
    currentScrollY:0,
    chatId:0,
    contact:[],
    urlApi:"http://domain1.whatsloop.localhost/livechat/",
    domain:"domain1"
  },
  mutations: {
    
    setCurrentScrollY (s, {y}) {
      s.currentScrollY = y;
    },
    ChangeLangAr(state) {
      return (state.lang = "ar");
    },
    ChangeLangEn(state) {
      return (state.lang = "en");
    },
    setchatId(state, payload) {
      state.chatId = payload;
    },
    setContact(state, payload) {
      state.contact = payload;
    },
    setError(state, payload) {
      state.error = payload;
    },
    clearError(state) {
      state.error = null;
    },
  },
  actions: {
    setCurrentScrollY ({ commit }, y) {
      commit('setCurrentScrollY', {y});
    },

    logout() {
      localStorage.removeItem("token");
    },
    chatIdAction({ commit }, payload) {
      commit("setchatId", payload.id);
    },
    contactAction({ commit }, payload) {
      commit("setContact", payload.contact);
    }
  },
  getters: {
    currentScrollY: s => s.currentScrollY,
    error(state) {
      return state.error;
    },
    chatId(state) {
      return state.chatId;
    },
    contact(state) {
      return state.contact;
    },
    urlApi(state) {
      return state.urlApi;
    },
    domain(state) {
      return state.domain;
    }
  },
});
