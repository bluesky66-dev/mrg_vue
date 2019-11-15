<template>
  <div id="momentum-action-plans" class="momentum-action-plans">
    <q-tabs>
      <q-tab
        default
        slot="title"
        name="tab-all"
        label="All plans"
      />
      <q-tab        
        slot="title"
        name="tab-current"
        :label="$t('action_plan.section.current')"
      />
      <q-tab
        slot="title"
        name="tab-drafts"
        label="Drafts"
      />
      <q-tab
        slot="title"
        name="tab-complete"
        :label="$t('action_plan.section.complete')"
      />
      <q-tab-pane name="tab-all">
        <momentum-action-plan-table :table-data="tableData.all" />
      </q-tab-pane>
      <q-tab-pane name="tab-current"> 
        <momentum-action-plan-table :table-data="tableData.current" />
      </q-tab-pane>
      <q-tab-pane name="tab-drafts">
        <momentum-action-plan-table :table-data="tableData.drafts" />
      </q-tab-pane>
      <q-tab-pane name="tab-complete">
        <momentum-action-plan-table :table-data="tableData.complete" />
      </q-tab-pane>
    </q-tabs>
  </div>
</template>
<style lang="scss" scoped>
  
  .momentum-action-plans {
    width: 100%;
  }

  .action-plan-card .q-card-main {
    margin-top: 1em;
    padding-top: 1em;
  }

  .action-plan-card .status-label {
    font-size: 14px;
    color: gray;
  }

  .action-plan-card .status-value {
    font-size: 20px;
  }

  ul.behaviors {
    list-style: none;
    padding: 0;
    margin: 0;
  }
</style>
<script>
import {
  QTabs,
  QTab,
  QTabPane,
  QDataTable,
  QCard,
  QCardMain,
  QCardTitle,
  QCardSeparator,
  QBtn,
  QIcon,
  QCardActions,
  QModal,
  QInnerLoading,
  QSpinner,
  QField,
  QInput,
  QTooltip,
  Dialog as QDialog,
} from 'quasar-framework';

import axios from 'axios';
import persistentApplicationStateMixin from '../../utils/persistentApplicationStateMixin';
import MomentumActionPlanTable from './MomentumActionPlanTable.vue';

export default {
  name: 'momentum-action-plans-index',
  mixins: [
    persistentApplicationStateMixin()
  ],
  data() {
    const appState = this.getApplicationStateFromPage();
    return {      
      createAppState: appState,
      confirmDeleteDraftModal: {
        inFlight: false
      },
      current: window.data.action_plans.filter(a => a.completed_at == null),
      complete: window.data.action_plans.filter(a => a.completed_at !== null),
      user: window.user,
      tableData: {
        current: [],
        drafts: [],
        complete: [],
        all: [],
      }
    };
  },
  components: {
    QTabs,
    QTab,
    QTabPane,
    QDataTable,
    QCard,
    QCardMain,
    QCardTitle,
    QCardSeparator,
    QBtn,
    QIcon,
    QCardActions,
    QModal,
    QInnerLoading,
    QSpinner,
    QField,
    QInput,
    QTooltip,
    QDialog,
    axios,
    MomentumActionPlanTable,
  },
  methods: {
    confirmDeleteDraft() {

    },
    deleteDraft() {
      const that = this;
      QDialog.create({
        title: this.$t('action_plan.continue_creating.message.title'),
        message: this.$t('action_plan.continue_creating.message.discard_confirmation_message'),
        buttons: [
          {
            label: this.$t('action_plan.continue_creating.message.discard'),
            color: 'primary',
            raised: true,
            handler () {
              return that
                .clearApplicationState('action_plan')
                .then(() => {
                  that.confirmDeleteDraftModal.inFlight = false;
                  that.navigateTo('/action-plans/create');
                });
            }
          },
          this.$t('global.cta.cancel'),
        ]
      });
    },
    navigateTo: function(nav) {
      window.location = nav;
    },
    openHelp() {
      window.trackEvent('get_help', 'view', 'action_plan.landing');
      window.open(
        this.$t('action_plan.help_url'),
        'newwindow',
        'width=800,height=500'
      );
    },
    deleteActionPlan(action_plan) {
      QDialog.create({
        title: this.$t('action_plan.delete.title'),
        message: this.$t('action_plan.delete.message'),
        buttons: [
          {
            label: this.$t('action_plan.delete.cta'),
            color: 'primary',
            raised: true,
            handler () {
              axios
                .post(`/api/action-plans/${action_plan.id}/delete`)
                .then(({data}) => {
                  if (data && data.redirect) {
                    if (data.message) {
                      window.sessionStorage.setItem('toast-message', data.message);
                    }
                    window.location = data.redirect;
                  }
                })
                .catch(() => {
                });
            }
          },
          this.$t('global.cta.cancel'),
        ]
      });
    }
  },
  computed: {
    hasDraft() {
      return Object.keys(this.createAppState).length > 0;
    },
    currentTableData() {
      return this.current.map(plan => {
        const {id, label, formatted_dates, behaviors} = plan;
        let _behaviours = [];
        let steps_completed = [];
        behaviors.map(behavior => {
          steps_completed.push(`${behavior.action_steps_complete.length}/${behavior.action_steps.length}`);
          _behaviours.push(behavior.label);
        });

        let status = 'Draft';
        if (!plan.completed_at) {
          status = 'Completed';
        } else if (plan.created_at) {
          status = 'Current';
        }

        return {
          id,
          label,
          status,
          steps_completed,
          behaviors: _behaviours,
          next_reminder_date: formatted_dates.next_reminder_date.localized,
          ends_at: formatted_dates.ends_at.localized,
          actions: '',
        };
      });
    }
  },
  beforeMount() {
    window.data.action_plans.map(plan => {
      const {id, label, formatted_dates, behaviors} = plan;
      let _behaviours = [];
      let steps_completed = [];
      behaviors.map(behavior => {
        steps_completed.push(`${behavior.action_steps_complete.length}/${behavior.action_steps.length}`);
        _behaviours.push(behavior.label);
      });

      const obj = {
        id,
        label,
        status: 'Current',
        steps_completed,
        behaviors: _behaviours,
        next_reminder_date: formatted_dates.next_reminder_date.localized,
        ends_at: formatted_dates.ends_at.localized,
        actions: '',
      };

      if (!plan.completed_at) {
        obj.status = 'Completed';
        this.tableData.complete.push(obj);
      } else if (plan.created_at) {
        this.tableData.current.push(obj);
      }
      this.tableData.all.push(obj);

    });
  },
  mounted() {
    console.log('Component action-plans mounted.');
    console.log(this.getApplicationStateFromPage());
    window.actionPlansIndex = this;
  }
};

</script>
