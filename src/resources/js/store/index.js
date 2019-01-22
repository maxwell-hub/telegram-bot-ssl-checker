import Vue from 'vue'
import Vuex from 'vuex'
import Api from '../api';

Vue.use(Vuex);

const PER_PAGE = 5;

export const store = new Vuex.Store({
    state: {
        subscribers: [],

        pagination: {
            total: 1,
            current: 1,
            perPage: PER_PAGE
        },
    },
    getters: {
        getSubscribers: (state) => {
            return state.subscribers;
        },
        subscribersCount: (state, getters) => {
            return getters.getSubscribers.length;
        },
        currentPage: (state) => {
            return state.pagination.current;
        },
        getPerPage: state => {
            return state.pagination.perPage
        },
        getTotal: state => {
            return state.pagination.total;
        },
    },
    mutations: {
        setList: (state, payload) => {
            state.subscribers = payload
        },
        setPagination: (state, payload) => {
            state.pagination.total = payload.total;
            state.pagination.current = payload.current_page;
        },
        setCurrentPage: (state, payload) => {
            state.pagination.current = payload;
        },
    },
    actions: {
        async loadSubscribersList({commit}, page) {
            const data = await Api.getSubscribers(
                page,
                this.getters.getPerPage
            );
            commit('setList', data.data);
            commit('setPagination', data);

        },
        async sendMessage({commit}, requestData) {
            const data = await Api.sendMessage(requestData.list, requestData.message);
        },
        async deleteSubscribers({commit}, selectedList) {
            await Api.deleteSubscribers(selectedList);
        },
        async populateSubscribers() {
            await Api.populate();
        },
        async cleanSubscribers() {
            await Api.clean();
        },
    }
});