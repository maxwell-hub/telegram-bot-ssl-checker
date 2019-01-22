import axios from "axios";

const API_BASE_URL = 'https://hexnet.tk/api/v1';
const API_ENDPOINT_GET_SUBSCRIBERS = API_BASE_URL + '/subscribers';
const API_ENDPOINT_SEND_MESSAGE = API_BASE_URL + '/subscribers/send-message';
const API_ENDPOINT_DELETE = API_BASE_URL + '/subscribers';
const API_ENDPOINT_POPULATE = API_BASE_URL + '/test/populate';
const API_ENDPOINT_CLEAN = API_BASE_URL + '/test/clean';


const api = axios.create({
    baseURL: API_ENDPOINT_GET_SUBSCRIBERS,
    withCredentials: false,
    headers: {
        "Accept": "application/json",
        "Content-Type": "application/json",
    }
});

class Api {
    async getSubscribers(curPage, perPage) {
        let queryParams = '?';
        if (curPage) {
            queryParams += 'page=' + curPage + '&';
        }
        if (perPage) {
            queryParams += 'per_page=' + perPage
        }
        const {data} = await api.get(API_ENDPOINT_GET_SUBSCRIBERS + queryParams);
        console.log('api request', data);
        return data;
    }

    async sendMessage(ids, message) {
        const {data} = api.post(API_ENDPOINT_SEND_MESSAGE, {
            ids: ids,
            message: message
        });
        return data;
    }

    async deleteSubscribers(ids) {
        let idsArr = ids.join(';');
        let queryParams = '?ids=' + idsArr;
        const {data} = api.delete(API_ENDPOINT_DELETE + queryParams);
        console.log('deleteSubscribers', data);
        return data;
    }

    async populate() {
        const {data} = api.get(API_ENDPOINT_POPULATE);
        return data;
    }

    async clean() {
        const {data} = api.get(API_ENDPOINT_CLEAN);
        return data;
    }
}

const _Api = new Api();

export default _Api;
