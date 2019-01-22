<template>
    <div class="row mt-5">

        <div class="col-md-6 offset-md-3">
            <div class="box">
                <div class="row">
                    <div class="col-md-12">
                        <h2>Telegram Subscribers [{{totalRecords}}]</h2>

                        <div class="subscribers-list">

                            <div class="table-container">
                                <table class="table table-filter">
                                    <tbody>
                                    <tr v-for="subscriber in subscribers"
                                        :key="subscriber.id"
                                        v-on:click="rowClickHandler(subscriber.id)"
                                        v-bind:class="{active: selectedSubscribers.includes(subscriber.id) }"
                                    >
                                        <td>
                                            <div class="ckbox">
                                                <input type="checkbox"
                                                       v-model="selectedSubscribers"
                                                       v-bind:value="subscriber.id"
                                                       v-bind:id="'checkbox-subscriber-' + subscriber.id">
                                                <label v-bind:id="'checkbox-subscriber-' + subscriber.id"></label>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="media">
                                                <div class="media-body">
                                                    <span class="media-meta pull-right">
                                                        {{ formatDate(subscriber.created_at) }}
                                                    </span>
                                                    <h4 class="title">
                                                        <a href="#">@{{ subscriber.username }}</a>
                                                        <span class="pull-right user"
                                                              v-bind:class="{user: subscriber.type === 'user', bot: subscriber.type === 'bot' }"
                                                        >({{subscriber.type }})</span>
                                                    </h4>
                                                    <p class="summary">{{subscriber.first_name}}
                                                        {{subscriber.last_name}}</p>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>

                        </div>

                        <b-pagination :total-rows="totalRecords"
                                      :per-page="perPage"
                                      v-model="currentPage"
                                      @change="pagination"
                                      align="center">
                        </b-pagination>

                        <div class="buttons-group">
                            <button class="btn btn-outline-primary"
                                    v-bind:disabled="hasSelected === 0"
                                    @click="clickSendMessageHandler"
                            >
                                <i class="fab fa-telegram-plane"></i> Send message
                            </button>
                            <b-button v-b-modal.modalWarning
                                      :variant="'outline-danger'"
                                      v-bind:disabled="hasSelected === 0"
                                      class="float-right"><i class="fas fa-times"></i> Delete
                            </b-button>
                        </div>

                        <div class="form-group message-box"
                             v-show="showMessageTestArea"
                        >
                            <label for="message-field">Enter your message to subscribers</label>
                            <textarea id="message-field"
                                      v-model="message"
                                      class="form-control"
                                      rows="3" minlength="1" maxlength="500"
                            ></textarea>
                            <button class="btn btn-outline-success float-right"
                                    v-bind:disabled="!message.length"
                                    @click="sendMessageHandler"
                            >
                                <i class="fab fa-telegram-plane"></i> Send
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="box mt-5">
                <div class="row">
                    <div class="col-md-12 text-center">
                        <div class="btn-group">
                            <button v-on:click="refreshHandle" type="button" class="btn btn-outline-info">
                                <i class="fas fa-sync-alt"></i> Refresh
                            </button>
                            <button v-on:click="populateHandle" type="button" class="btn btn-outline-secondary">
                                <i class="fas fa-plus"></i> Populate
                            </button>
                            <button v-on:click="cleanHandle" type="button" class="btn btn-outline-danger">
                                <i class="fas fa-trash-alt"></i> Clean
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Component -->
        <b-modal id="modalWarning" @ok="handleModalOk" title="Action conformation">
            <p class="my-4">These subscribers will be removed. Do you sure want to continue?</p>
        </b-modal>

    </div>
</template>

<script>

    import DateFormat from '../dateformat';
    import bModal from 'bootstrap-vue/es/components/modal/modal';
    import bModalDirective from 'bootstrap-vue/es/directives/modal/modal';
    import bButton from 'bootstrap-vue/es/components/button/button';
    import bPagination from 'bootstrap-vue/es/components/pagination/pagination';

    export default {
        name: "App",
        components: {
            bModal,
            bButton,
            bPagination
        },
        directives: {
            'b-modal': bModalDirective
        },
        created() {
            this.$store.dispatch('loadSubscribersList')
        },
        data() {
            return {
                selectedSubscribers: [],
                currentPage: 1,
                isMessageAreaShown: false,
                message: '',
            }
        },
        computed: {
            subscribers() {
                console.log('call to get subscribers');
                return this.$store.getters.getSubscribers;
            },
            hasSelected() {
                return this.selectedSubscribers.length;
            },
            perPage() {
                return this.$store.getters.getPerPage;
            },
            totalRecords() {
                return this.$store.getters.getTotal;
            },
            showMessageTestArea() {
                return this.hasSelected && this.isMessageAreaShown
            }
        },
        methods: {
            pagination(e) {
                this.$store.dispatch('loadSubscribersList', e);
            },
            rowClickHandler(id) {
                if (this.selectedSubscribers.includes(id)) {
                    this.selectedSubscribers.splice(this.selectedSubscribers.indexOf(id), 1)
                } else {
                    this.selectedSubscribers.push(id)
                }
                if (this.hasSelected) {
                    this.isMessageAreaShown = false;
                }
            },
            clickSendMessageHandler() {
                this.isMessageAreaShown = true;
            },
            handleModalOk(e) {
                console.log('handleModalOk', e);
                this.$store.dispatch('deleteSubscribers', this.selectedSubscribers)
                    .then(() => {
                        this.selectedSubscribers = [];
                        console.log('DELETE IS DONE. MAKE REQUEST TO UPDATE');
                        this.refreshHandle();
                    })
            },
            sendMessageHandler() {
                if (!this.message.length) {
                    return false;
                }
                this.$store.dispatch('sendMessage', {
                    list: this.selectedSubscribers,
                    message: this.message
                })
                    .then(() => {
                        alert('Message has been sent to ' + this.selectedSubscribers.length + ' recipients')
                    });
            },
            refreshHandle() {
                this.currentPage = 1;
                this.selectedSubscribers = [];
                this.$store.dispatch('loadSubscribersList', 1);
            },
            populateHandle() {
                this.$store.dispatch('populateSubscribers')
                    .then(() => {
                        this.refreshHandle();
                    })
            },
            cleanHandle() {
                this.$store.dispatch('cleanSubscribers')
                    .then(() => {
                        this.refreshHandle();
                    })
            },
            formatDate(date) {
                return DateFormat.formatDate(date);
            }
        }
    }
</script>

<style>
    h1, h2, h3, h4, h5, h6 {
        margin-bottom: 0.5em;
    }

    .box {
        padding: 16px;
        background-color: #fff;
        -webkit-box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
        border-radius: 4px;
        max-height: 100%;
        overflow-y: auto;
    }

    .btn-outline-info:hover {
        color: #fff;
    }

    .subscribers-list, nav.pagination-subscribers-list, .message-box textarea {
        margin-bottom: 0.75em;
    }

    nav.pagination-subscribers-list {
        display: flex;
        justify-content: center;
    }

    .message-box {
        margin-top: 0.75em;
    }

    .message-box textarea {
        min-height: 84px;
    }

    /*--------------------------------------------------
    :: Table
    -------------------------------------------------- */
    .table-filter {
        background-color: #fff;
        border-bottom: 1px solid #eee;
    }

    .table-filter tbody tr:hover {
        cursor: pointer;
        background-color: #eee;
    }

    .table-filter tbody tr.active {
        background-color: #eee;
    }

    .table-filter tbody tr td {
        padding: 0 0 0 10px;
        vertical-align: middle;
        border-top-color: #eee;
    }

    .table-filter tbody tr.selected td {
        background-color: #eee;
    }

    .table-filter tr td:first-child {
        width: 10px;
    }

    .table-filter tr td:nth-child(2) {
        width: 35px;
    }

    .ckbox {
        position: relative;
    }

    .ckbox input[type="checkbox"] {
        opacity: 0;
    }

    .ckbox label {
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }

    .ckbox label:before {
        content: '';
        top: 1px;
        left: 0;
        width: 18px;
        height: 18px;
        display: block;
        position: absolute;
        border-radius: 2px;
        border: 1px solid #bbb;
        background-color: #fff;
    }

    .ckbox input[type="checkbox"]:checked + label:before {
        border-color: #2BBCDE;
        background-color: #2BBCDE;
    }

    .ckbox input[type="checkbox"]:checked + label:after {
        top: 2px;
        left: 3.5px;
        content: '\f00c';
        color: #fff;
        font-size: 11px;
        font-family: "Font Awesome 5 Free";
        font-weight: 900;
        position: absolute;
    }

    .table-filter .media-body {
        display: block;
    }

    .table-filter .media-meta {
        font-size: 11px;
        color: #999;
    }

    .table-filter .media .title {
        color: #2BBCDE;
        font-size: 14px;
        font-weight: bold;
        line-height: normal;
        margin: 0;
    }

    .table-filter .media .title span {
        font-size: .8em;
        margin-right: 20px;
    }

    .table-filter .media .title span.user {
        color: #5cb85c;
    }

    .table-filter .media .title span.bot {
        color: #f0ad4e;
    }

    .table-filter .media .summary {
        font-size: 14px;
    }
</style>