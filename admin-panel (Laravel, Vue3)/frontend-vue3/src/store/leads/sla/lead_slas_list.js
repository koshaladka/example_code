import { defineStore } from 'pinia';
import api from '@/api/leads/lead_slas';

export const useLeadSlasListStore = defineStore({
  id: 'lead_slas_list',
  state: () => ({
    leadSlasList: [],

    leadSlasListLoading: false,
    leadSlasListHeaders: [
      {
        name: 'time_from',
        label: 'Время SLA (ч.)',
        align: 'center',
        sortable: true,
        field: 'time_from',
        style: 'width: 150px',
      },
      {
        name: 'lead_status_id',
        align: 'left',
        label: 'Статус лида',
        field: 'status',
        sortable: true,
      },
      {
        name: 'color',
        label: 'Код цвета строки в ЛКП',
        field: 'color',
        align: 'left',
        sortable: true,
        style: 'width: 200px',
      },
      {
        name:
            'actions',
        label: 'Действие',
        field: 'actions',
        align: 'left',
        style: 'width: 200px',
      },
    ],

    statuses: [],
    colors: [],

    filters: {
      leadSlasListFilterStatus: [],
      leadSlasListFilterColor: [],
    },
    pagination: {
      sortBy: 'time_from',
      descending: false,
      page: 1,
      rowsPerPage: 10,
      rowsNumber: 0,
    },
  }),
  actions: {
    async getLeadSlas() {
      this.leadSlasListLoading = true;
      const filters = JSON.stringify({
        statuses: this.filters.leadSlasListFilterStatus,
        colors: this.filters.leadSlasListFilterColor,
      });
      const params = {
        filters,
        page: this.pagination.page,
        limit: this.pagination.rowsPerPage,
        sort: this.pagination.sortBy,
        sort_type: this.pagination.descending ? 'desc' : 'asc',
      };

      try {
        const { data, meta } = await api.getLeadSlas(params);

        this.leadSlasList = data.items.map((b) => ({
          ...b,
        }));

        this.statuses = data.statuses.map((b) => ({
          ...b,
        }));

        this.colors = data.colors.map((b) => ({
          ...b,
        }));

        this.pagination.page = meta.current_page || 1;
        this.pagination.rowsPerPage = meta.limit || 50;
        this.pagination.rowsNumber = meta.total_pages || data.items.length;
      } catch (e) {
        console.error(e);
      } finally {
        this.leadSlasListLoading = false;
      }
    },

    async deleteLeadSla(id) {
      try {
        const data = await api.deleteLeadSla(id);

        if (data.success) {
          await this.getLeadSlas();
        }
      } catch (error) {
        console.error(error);
      }
    },

    UPDATE_PAGINATION(value) {
      this.pagination = { ...value };
    },
    UPDATE_PAGINATION_KEY({ key, value }) {
      this.pagination[key] = value;
    },
    UPDATE_FILTERS({ key, value }) {
      this.filters[key] = value;
    },
    UPDATE_PAGINATION_FILTER(value) {
      this.pagination = { ...value.pagination };

      if (!value.pagination.sortBy) {
        this.pagination.sortBy = value.pagination.sortBy;
        this.pagination.descending = value.pagination.descending;
      }
    },

    UPDATE_FILTERS_DATE_FROM(value) {
      this.filters.date_from = value;
    },

    UPDATE_FILTERS_DATE_TO(value) {
      this.filters.date_to = value;
    },

  },
});
