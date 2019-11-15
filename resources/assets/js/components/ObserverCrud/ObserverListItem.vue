<template>
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
</template>

<script>

import {
  QItem,
  QItemMain,
  QItemSeparator,
  QBtn,
} from 'quasar-framework';

export default {
  data(){
    return {};
  },
  components: {
    QItem,
    QItemMain,
    QItemSeparator,
    QBtn,
  },
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
</script>

