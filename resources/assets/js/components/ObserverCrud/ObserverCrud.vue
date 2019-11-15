<template>
  <div>
    <q-modal
      ref="editObserverModal" 
      position="top" 
      noBackdropDismiss
      noEscDismiss>
       <edit-observer
        ref="editObserver" 
        :observerTypes="observer_types"
        :cultures="cultures"
        @successful="editedObserver"
        @cancel="$refs.editObserverModal.close()"
        />
    </q-modal>
    <q-list class="observer-crud"  highlight separator>
      <q-list-header>
        <h5>{{ $t('profile.observer_directory') }}</h5>
      </q-list-header>
      <div class="q-item-separator-component"></div>
      <observer-list-item 
        v-for="observer in observers" 
        :key="observer.id" 
        :observer="observer"
        :id="observer.id" 
        :full-name="observer.full_name"
        :email="observer.email"
        :type="observer.observer_type_translated"
        @editObserver="editObserver"
        @deleteObserver="deleteObserver"/>
    </q-list>
    <div class="after-list">
      <q-btn 
        color="primary" 
        icon="person_add" 
        @click="addObserver">
        {{ $t('profile.contact.add_contact.title') }}
      </q-btn>    
    </div>
  </div>
</template>


<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
h1, h2 {
  font-weight: normal;
}
h5 {
  margin: 0;
  padding: 0.3em 0;
}


.observer-crud >>> .observer-list-item{
  
}

.observer-crud >>> .observer-list-item .observer{
 flex: 1 0 auto;
 margin: 0 1em;
}
.observer-crud >>> .observer-list-item .observer .observer-name-type{
  font-size: 1.4em;
  line-height: 1;
}
.observer-crud >>> .observer-list-item .observer .observer-email{
 font-size: 1.2em;
}

.after-list{
  margin-top: 1em;
}

</style>

<script>
import get from 'lodash/get';
import {
  QList,
  QListHeader,
  QItem,
  QItemMain,
  QBtn,
  QItemSeparator,
  // QCardMain,
  // QCardTitle,
  // QCardSeparator,
  // QField,
  // QBtn,
  // QInput,
  // QCardActions,
  QModal,
  // Toast,
  // QInnerLoading,
  // QSpinner,
  // QSelect
} from 'quasar-framework';

import EditObserver from './EditObserver';

const ObserverListItem = {
  data(){
    return {};
  },
  components: {
    QItem,
    QItemMain,
    QItemSeparator,
    QBtn,
  },
  template: `
    <q-item class="observer-list-item">
      <q-item-main  class="">
        <div class="row items-center">
          <q-btn 
            class="" 
            flat 
            round 
            color="primary" 
            icon="mode_edit"
            @click="editObserver"/>
            <div class="observer column">
              <div class="observer-name-type">
                {{ nameTypeLabel }}
              </div>
              <div class="observer-email">
                {{ email }}
              </div>
            </div>
          <q-btn 
            class=""
            flat
            round
            color="faded"
            icon="delete"
            @click="deleteObserver" />
        </div>
      </q-item-main >
    </q-item>
  `,
  props: [
    'observer',
    'id',
    'full-name',
    'email',
    'type',
  ],
  computed:{
    nameTypeLabel(){
      return this.fullName + ' - ' + this.type;
    }
  },
  methods:{
    editObserver(){
      this.$emit('editObserver',this.id);
    },
    deleteObserver(){
      this.$emit('deleteObserver',this.id);
    }
  }
};

export default {
  name: 'observer-crud',
  components: {
    QList,
    QListHeader,
    QItem,
    QItemMain,
    ObserverListItem,
    QBtn,
    QModal,
    EditObserver
  },
  data:function () {
    return {
      observers:window.data.observers,
      observer_types:window.data.observer_types,
      cultures:window.cultures,
      focusObserver:null,
      defaultCultureId:1
    };
  },
  computed:{


  },
  methods:{
    addObserver(){
      console.log('addObserver');
      this.focusObserver = null;
      //this.defaultCultureId = get(window,'profile.culture.id',1);
      console.log('addObserver',Object.keys(this.observers));
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
    editObserver(id){
      console.log('editObserver',id);
      const observer = this.observers.find(o=>o.id === id);
      console.log('observer',observer);
      
      this.$refs.editObserver.setDefaultValues({
        id:id,
        mode:'edit',
        first_name:observer.first_name,
        last_name:observer.last_name,
        email:observer.email,
        observer_type:observer.observer_type,
        culture_id:observer.culture_id,
      });
      this.$refs.editObserverModal.open();
    },
    editedObserver({action,payload}){
      //console.log('editedObserver',{action,payload});
      this.$refs.editObserverModal.close();
      if(action === 'create'){
        this.$set(this.observers,this.observers.length,payload);
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
    deleteObserver(id){
      //console.log('deleteObserver',id,this.observers);
      const observer = this.observers.find(o=>o.id === id);
      this.$refs.editObserver.setDefaultValues({
        id:id,
        mode:'delete',
        first_name:observer.first_name,
        last_name:observer.last_name,
        email:observer.email,
        observer_type:observer.observer_type,
        culture_id:observer.culture_id,
      });
      this.$refs.editObserverModal.open();
    }
  }
};
</script>

