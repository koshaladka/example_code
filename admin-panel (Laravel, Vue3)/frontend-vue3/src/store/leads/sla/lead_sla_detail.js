import { defineStore } from 'pinia';
import api from '@/api/leads/lead_slas';

const default_lead_sla = {
  time_from: null,
  lead_status_slug: null,
  color: null,
};

export const useLeadSlasDetailStore = defineStore({
  id: 'lead_slas_detail',
  state: () => ({

    new_lead_sla: { ...default_lead_sla },

    current_lead_sla: { ...default_lead_sla },

    lead_statuses: [],

    loading: false,

    is_lead_sla_published: false,

    is_modal_lead_sla_validate: false,

    is_error_published: false,

    is_error_published_message: null,

  }),
  actions: {
    async getLeadSla(id) {
      try {
        this.loading = true;

        const { data } = await api.getLeadSla(id);

        this.current_lead_sla = {
          ...data,
        };

        this.new_lead_sla = { ...this.current_lead_sla };
      } catch (error) {
        console.error(error);
      } finally {
        this.loading = false;
      }
    },

    async addLeadSla() {
      try {
        this.is_error_published_message = null;
        const form_data = new FormData();

        if (this.new_lead_sla.lead_status_slug) {
          form_data.append('lead_status', this.new_lead_sla.lead_status_slug);
        }

        if (this.new_lead_sla.time_from) {
          form_data.append('time_from', this.new_lead_sla.time_from);
        }

        if (this.new_lead_sla.color) {
          form_data.append('color', this.new_lead_sla.color);
        }

        const data = await api.addLeadSla(form_data);

        if (data) {
          if (data.code === 201) {
            this.is_lead_sla_published = true;
          }
        }
      } catch (e) {
        this.is_error_published = true;
        this.is_error_published_message = e.message;
      }
    },

    async editLeadSla(id) {
      try {
        this.is_error_published_message = null;
        const form_data = new FormData();

        if (this.new_lead_sla.lead_status_slug) {
          form_data.append('lead_status', this.new_lead_sla.lead_status_slug);
        }

        if (this.new_lead_sla.time_from) {
          form_data.append('time_from', this.new_lead_sla.time_from);
        }

        if (this.new_lead_sla.color) {
          form_data.append('color', this.new_lead_sla.color);
        }

        const data = await api.editLeadSla(id, form_data);

        if (data.code === 202) {
          this.is_lead_sla_published = true;
        }
      } catch (e) {
        this.is_error_published = true;
        this.is_error_published_message = e.message;
      }
    },

    async getLeadStatuses() {
      const params = {
        limit: 9999,
        page: 1,
      };
      const { data } = await api.getLeadStatuses(params);

      this.lead_statuses = data.data.map((b) => ({
        ...b,
      }));
    },

    UPD_NEW_LEAD_SLA_DETAIL({ key, value }) {
      this.new_lead_sla[key] = value;
    },

    SET_CLEAR_NEW_LEAD_SLA() {
      this.new_lead_sla = { ...default_lead_sla };
    },

    UPD_LEAD_SLA_PUBLISHED_TOGLE(value) {
      this.is_lead_sla_published = value;
    },

    UPD_LEAD_SLA_VALIDATE_TOGLE(value) {
      this.is_modal_lead_sla_validate = value;
    },

    UPD_LEAD_SLA_ERROR_PUBLISHED_TOGLE(value) {
      this.is_error_published = value;
    },

    VALIDATE_NEW_LEAD_SLA() {
      return this.new_lead_sla.lead_status_slug != null
          && this.new_lead_sla.color != null
          && !Number.isNaN(parseFloat(this.new_lead_sla.time_from));
    },
  },
});
