<template>
  <div>
    <v-client-table ref="table" :columns="columns" :data="tableData" :options="options">
      <template slot="label" scope="props">
        <div>{{ props.row.label }}</div>
        <span :class="`col-status col-status--${props.row.status}`">{{ props.row.status }}</span>
      </template>
      <template slot="behaviors" scope="props">
        <div v-for="(behavior, index) in props.row.behaviors" :key="index">
          {{ behavior }}
        </div>
      </template>
      <template slot="steps_completed" scope="props">
        <div v-for="(item, index) in props.row.steps_completed" :key="index">
          {{ item }}
        </div>
      </template>
      <template slot="actions" scope="props">            
        <div>
          <q-btn
            flat
            round
            icon="description"
            color="primary"
            @click="navigateTo(`/action-plans/${props.row.id}`)"
            small
          >
          </q-btn>
          <q-btn
            flat
            round
            icon="share"
            color="primary"
            @click="navigateTo(`/action-plans/${props.row.id}/share`)"
            small
          >
          </q-btn>              
        </div>
      </template>
    </v-client-table>
  </div>
</template>
<style lang="scss" scoped>
  @import "~@/_variables.scss";
  .col-status {
    font-size: 1rem;
    &--Current {
      color: $color-dark-blue;
    }
    &--Completed {
      color: $color-teal;
    }
    &--Draft {
      color: $color-orange;
    }
  }
</style>
<script>
import {
  QDataTable,
  QBtn,
} from 'quasar-framework';

export default {
  name: 'momentum-action-plan-table',
  data() {
    return {
      columns: [
        'label',
        'behaviors',
        'steps_completed',
        'ends_at',
        'next_reminder_date',        
        'actions',
      ],
      options: {
        headings: {
          label: 'Action Plan',
          behaviors: 'Behaviors',
          steps_completed: 'Steps completed',
          ends_at: 'Target Date',
          next_reminder_date: 'Next Reminder',
          actions: 'Actions',
        },
        filterable: false,
        texts:{
          count:''
        }
      },
    };
  },
  components: {
    QDataTable,
    QBtn,
  },
  props: {
    tableData: {
      required: true,
      type: Array
    }
  },
  methods: {
    navigateTo: function(nav) {
      window.location = nav;
    },
  }
};
</script>
