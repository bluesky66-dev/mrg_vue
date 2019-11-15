<template>
    <div id="momentum-journal"
        class="momentum-journal">
        <h3>{{ $t('journal.title') }}</h3>
        <q-card class="with-hover">
            <div class="hover-over-card-top-right">
                <q-btn v-if="entries.length"
                    icon="email"
                    color="primary"
                    @click="startSharing"
                    flat>
                    {{ $t('journal.cta.share_entries') }}
                </q-btn>
                <q-btn v-on:click.prevent="openHelp"
                    icon="help"
                    class=""
                    color="positive"
                    flat>
                    {{ $t('global.nav.get_help') }}
                </q-btn>
            </div>
            <q-card-main>
                <q-field icon="search">
                    <q-input v-model="searchText">
                    </q-input>
                </q-field>
            </q-card-main>
        </q-card>
        <q-card class="relative-position">
            <q-card-main>
                <q-btn v-if="!(isEditing || isSharing)"
                    flat
                    @click="newEntry"
                    color="primary"
                    icon="edit">
                    {{ $t('journal.cta.new_entry') }}
                </q-btn>
                <div v-if="isEditing">
                    <q-field
                        :error="$v.description.$error"
                        :error-label="descriptionErrorMsg">
                        <q-input @input="descriptionDelayedTouch"
                            v-model="description"
                            :float-label="$t('journal.description_label')"
                            type="textarea">
                        </q-input>
                    </q-field>
                    <q-field icon="label"
                        color="primary">
                        <q-select multiple
                            chips
                            :float-label="$t('journal.cta.tag')"
                            color="primary"
                            v-model="selectedBehaviors"
                            :options="behaviorOptions"></q-select>
                    </q-field>
                    <div style="clear: both; margin-bottom: 16px;"></div>
                    <q-btn flat
                        big
                        @click="discardEntry"
                        class="pull-right"
                        color="primary">
                        {{ $t('global.cta.discard') }}
                    </q-btn>
                    <q-btn @click="saveEntry"
                        flat
                        big
                        class="pull-right"
                        color="primary">
                        {{ $t('global.cta.save') }}
                    </q-btn>
                    <div style="clear: both"></div>
                </div>
                <div v-if="isSharing">
                    <div class="row">
                        <q-field
                                :error="$v.selectedObservers.$error || $v.selectedEntries.$error"
                                :error-label="[selectedObserversErrorMsg,selectedEntriesErrorMsg].find(x => x)"
                                class="col-7">
                            <q-select multiple
                                chips
                                color="primary"
                                :float-label="$t('journal.send_to')"
                                v-model="selectedObservers"
                                :options="observerOptions"></q-select>
                        </q-field>
                        <div class="col"></div>
                        <div class="col-4 column justify-center">
                            <q-btn color="primary"
                                icon="person_add"
                                @click="addObserver">
                                {{ $t('global.cta.add_recipient') }}
                            </q-btn>
                        </div>
                    </div>
                    <div style="clear: both; margin-bottom: 16px;"></div>
                    <q-btn flat
                        big
                        @click="stopSharing"
                        class="pull-right"
                        color="primary">
                        {{ $t('global.cta.back') }}
                    </q-btn>
                    <q-btn @click="sendShare"
                        flat
                        big
                        class="pull-right"
                        color="primary">
                        {{ $t('journal.cta.share_entries') }}
                    </q-btn>
                    <div style="clear: both"></div>
                </div>
                <q-inner-loading :visible="inFlight">
                    <q-spinner size="50px"
                        color="primary" />
                </q-inner-loading>
            </q-card-main>
        </q-card>
        <div id="search-results">
            <div class="row center-items"
                v-if="isSharing">
                <div class="col-1">
                </div>
                <div class="col-11">
                    <h3>{{ $t('journal.select_entries') }}</h3>
                </div>
                <div class="col-1 text-center">
                </div>
                <div class="col-11">
                    <q-btn @click="selectAllEntries"
                        style="margin-left: 8px;"
                        big>
                        <span v-if="!allSelected">{{ $t('journal.cta.select_all') }} </span>
                        <span v-if="allSelected">{{ $t('journal.cta.deselect_all') }} </span>
                    </q-btn>
                </div>
            </div>
            <div v-for="entry in entryResults"
                :key="entry.id">
                <div class="row center-items">
                    <div class="col-1 text-center">
                        <q-field v-if="isSharing">
                            <q-checkbox v-model="selectedEntries"
                                :id="entry.id"
                                :val="entry.id"                                 
                                />
                        </q-field>
                    </div>
                    <div v-bind:class="{'col-11': isSharing, 'col-12': !isSharing}">
                        <q-card>
                            <q-card-main>
                                <q-icon
                                        color="primary"
                                        size="20px"
                                        name="more_vert"
                                        @click="resetSharing()"
                                        class="pull-right">
                                    <!-- Direct child of target -->
                                    <q-popover ref="popover">
                                        <q-list item-separator link>
                                            <q-item
                                                    @close="$refs.popover[entry.id].close()" 
                                                    @click="editEntry(entry)">
                                                <q-item-side color="primary" icon="edit" />
                                                <q-item-main :label="$t('journal.cta.edit')" />
                                            </q-item>
                                            <q-item @close="$refs.popover[entry.id].close()" @click="deleteEntry(entry)">
                                                <q-item-side color="negative" icon="delete" />
                                                <q-item-main :label="$t('journal.cta.delete')" />
                                            </q-item>
                                        </q-list>
                                    </q-popover>
                                </q-icon>
                                <div class="date">{{ entry.formatted_dates.created_at.localized }}</div>
                                <p class="body" v-bind:class="{scrollable: entry.description.length > 520}"> {{ entry.description }}</p>
                                <p v-if="entry.behavior_tags"
                                    class="tags pull-right">
                                    <q-icon size="2rem"
                                        color="primary"
                                        name="label" /> {{ entry.behavior_tags }}
                                </p>
                                <div style="clear: both;"></div>
                            </q-card-main>
                        </q-card>
                    </div>
                </div>
            </div>
        </div>
        <q-modal ref="editObserverModal"
            position="top"
            noBackdropDismiss
            noEscDismiss>
            <edit-observer ref="editObserver"
                :observerTypes="observer_types"
                :cultures="cultures"
                @successful="editedObserver"
                @cancel="$refs.editObserverModal.close()" />
        </q-modal>
    </div>
</template>

<style lang="scss" scoped>
    .momentum-journal {
      // max-width: 700px;
    }
    .tags{
        color: #027be3;
        line-height: 2rem;
        font-size: 16px;
        vertical-align: middle;
        margin-bottom: 0px;
    }
    .date{
        font-size: 16px;
        color: #747474;
    }
    .body{
        font-size: 16px;
    }
    .center-items{
        align-items: center;
    }
    .scrollable{
        max-height: 150px;
        overflow: auto;
    }
</style>
<script>
import {
  QCard,
  QCardMain,
  QCardTitle,
  QCardSeparator,
  QBtn,
  QField,
  QInput,
  QIcon,
  QSpinner,
  QSelect,
  QCheckbox,
  QInnerLoading,
  QModal,
  QList,
  QPopover,
  QItem,
  QItemMain,
  QItemSide,
  Toast,
  scroll,
} from 'quasar-framework';

import EditObserver from './ObserverCrud/EditObserver.vue';
import get from 'lodash/get';

import axios from 'axios';

import {required as requiredValidator} from 'vuelidate/lib/validators';
import {createServerValidation} from '../utils/serverValidationMixin';

const serverValidation = createServerValidation({
  parameters: [
    {
      name: 'description',
      frontValidations: [
        {
          name: 'required',
          validator: requiredValidator,
          errorMsg: function () {
            return this.$t('global.validation.required', {
              attribute: this.$t('global.validation.attributes.description')
            });
          }
        }
      ]
    },
    {
      name: 'selectedObservers',
      frontValidations: [
        {
          name: 'required',
          validator: requiredValidator,
          errorMsg: function () {
            return this.$t('journal.validation.send_to');
          }
        }
      ]
    },
    {
      name: 'selectedEntries',
      frontValidations: [
        {
          name: 'required',
          validator: requiredValidator,
          errorMsg: function () {
            return this.$t('journal.validation.share_entries');
          }
        }
      ]
    }
  ],
  initData() {
    return window.data;
  }
});

export default {
  name:'momentum-journal',
  mixins: [serverValidation],
  components: {
    QCard,
    QCardMain,
    QCardTitle,
    QCardSeparator,
    QIcon,
    QField,
    QInput,
    QSpinner,
    QSelect,
    QInnerLoading,
    QBtn,
    QCheckbox,
    Toast,
    QModal,
    QList,
    QItem,
    QItemMain,
    QItemSide,
    QPopover,
    scroll,
    EditObserver
  },
  data() {
    const data = {
      inFlight: false,
      searchText: '',
      isSharing: false,
      entries: window.data.entries,
      entryResults: window.data.entries,
      isEditing: false,
      currentEntryId: null,
      description: '',
      observers:window.data.observers,
      observer_types:window.data.observer_types,
      cultures:window.cultures,
      selectedObservers: [],
      allSelected: false,
      selectedEntries: [],
      selectedBehaviors: [],
      behaviors: window.behaviors,
      options: {
        keys: ['description', 'id', 'behavior_tags']
      }
    };
    return data;
  },
  methods: {
    openHelp() {
      if (this.isSharing) {
        window.trackEvent('get_help', 'view', 'journal.sharing');
      } else {
        window.trackEvent('get_help', 'view', 'journal.landing');
      }
      window.open(
        this.$t('journal.help_url'),
        'newwindow',
        'width=800,height=500'
      );
    },
    newEntry() {
      this.$v.$reset();
      this.isEditing = true;
    },
    discardEntry() {
      this.selectedBehaviors = [];
      this.description = [];
      this.currentEntryId = null;
      this.isEditing = false;
    },
    sendShare() {
      if (this.$v.selectedObservers.$invalid || this.$v.selectedEntries.$invalid) {
        this.$v.selectedObservers.$touch();
        this.$v.selectedEntries.$touch();
        return;
      }
      this.$v.$reset();
      this.inFlight = true;
      axios
        .post('/api/journal-entries/share', {
          observers: this.selectedObservers,
          entries: this.selectedEntries,
        })
        .then(({data}) => {
          Toast.create(data.message);
          this.isSharing = false;
          this.selectedObservers = [];
          this.selectedEntries = [];
          this.allSelected = false;
        })
        .catch(({response}) => {
          if (response.status === 422) {
            const errors = get(response, 'data.errors');

            this.errors = {
              ...this.errors,
              ...errors
            };

            this.$v.$touch();
          }
          //console.dir(response);
        })
        .then(() => {
          this.inFlight = false;
        });
    },
    saveEntry() {
      if (this.$v.description.$invalid) {
        this.$v.description.$touch();
        return;
      }
      this.$v.$reset();
      this.inFlight = true;

      var url = '/api/journal-entries';

      // if there's a current entry id set, we're updating
      if (this.currentEntryId) {
        url = `/api/journal-entries/${this.currentEntryId}/update`;
      }

      axios
        .post(url, {
          description: this.description,
          behaviors: this.selectedBehaviors,
        })
        .then(({data}) => {
          Toast.create(data.message);
          this.updateEntries();
          this.isEditing = false;
          this.description = '';
          this.selectedBehaviors = [];
          this.currentEntryId = null;
        })
        .catch(({response}) => {
          if (response.status === 422) {
            const errors = get(response, 'data.errors');

            this.errors = {
              ...this.errors,
              ...errors
            };

            this.$v.$touch();
          }
          //console.dir(response);
        })
        .then(() => {
          this.inFlight = false;
        });
    },
    updateEntries() {
      this.inFlight = true;
      axios
        .get('/api/journal-entries' )
        .then(({data}) => {
          this.entries = data;
          this.entryResults = data;
          this.searchText = '';
        })
        .catch(() => {
        })
        .then(() => {
          this.inFlight = false;
        });
    },
    startSharing() {
      this.isEditing = false;
      this.isSharing = true;
    },
    resetSharing() {
      this.isSharing = false;
      this.selectedObservers = [];
      this.selectedEntries = [];
      this.allSelected = false;
    },
    stopSharing() {
      this.resetSharing();
    },
    selectAllEntries() {
      // if all of the entries are already selected, we want to deselect them
      if (this.allSelected) {
        this.selectedEntries = [];
        this.allSelected = false;
      } else {
        this.selectedEntries = this.entries.map(entry => {
          return entry.id;
        });
        this.allSelected = true;
      }
    },
    addObserver(){
      this.$refs.editObserver.setDefaultValues(
        {id:null,
          mode:'create',
          first_name:null,
          last_name:null,
          email:null,
          observer_type:Object.keys(this.observer_types)[0],
          culture_id:get(window,'profile.culture.id',1)
        });
      this.$refs.editObserverModal.open();
    },
    editedObserver({action,payload}){
      //console.log('editedObserver',{action,payload});
      this.$refs.editObserverModal.close();
      if(action === 'create'){
        this.$set(this.observers,this.observers.length,payload);
        this.$set(this.selectedObservers,this.selectedObservers.length,payload.id);
      }
      if(action === 'edit'){
        this.observers.forEach((observer,idx)=>{
          if(observer.id === payload.id ){
            this.$set(this.observers,idx,payload);
          }
        });
      }
      if(action === 'delete'){
        this.observers.forEach((observer,idx)=>{
          if(observer.id === payload.id ){
            this.$delete(this.observers,idx);
          }
        });
      }
    },
    deleteEntry(entry){
      axios
        .post(`/api/journal-entries/${entry.id}/delete`)
        .then(({data}) => {
          Toast.create(data.message);
          this.updateEntries();
        })
        .catch(() => {
        });
      this.$refs.popover.forEach(function(popover){
        popover.close();
      });
    },
    editEntry(entry) {
      this.isEditing = true;
      this.description = entry.description;
      this.selectedBehaviors = entry.behaviors.map(function(behavior) {
        return behavior.id;
      });
      this.currentEntryId = entry.id;
      this.$refs.popover.forEach(function(popover){
        popover.close();
      });
      const { setScrollPosition, getScrollTarget } = scroll;
      var target = getScrollTarget(this.$el);
      setScrollPosition(target, 0, 500);
    },
  },
  watch: {
    searchText () {
      if (this.searchText == '') {
        this.entryResults = this.entries;
      } else {
        this.$search(this.searchText, this.entries, this.options).then(results => {
          this.entryResults = results;
        });
      }
    },
    selectedEntries : function(r){
      this.allSelected = false;
      if(r.length === this.entryResults.length){
        // console.log("All elements have been manually selected.");
        this.allSelected = true;
      }
    }
  },
  computed: {
    behaviorOptions() {
      if (!this.behaviors) {
        return [];
      } else {
        return this.behaviors.map(behavior => ({
          value: behavior.id,
          label: behavior.name_key_translated
        }));
      }
    },
    observerOptions() {
      if (!this.observers) {
        return [];
      } else {
        return this.observers.map(observer => ({
          value: observer.id,
          label: observer.full_name + ' (' + observer.email + ')'
        }));
      }
    }
  },
  mounted() {
    console.log('Component journal mounted.');
  }
};
</script>
