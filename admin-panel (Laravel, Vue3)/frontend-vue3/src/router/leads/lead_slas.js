const lead_slas = [
  {
    path: '/lead_slas',
    name: 'lead_slas',
    // route level code-splitting
    // this generates a separate chunk (About.[hash].js) for this route
    // which is lazy-loaded when the route is visited.
    component: () => import('@/pages/leads/sla/LeadSlaList.vue'),
    meta: {
      layout: 'MainLayout',
      page: 'main_lead_slas',
      visible: true,
    },
  },
  {
    path: '/lead_slas/new',
    name: 'new_lead_sla',
    // route level code-splitting
    // this generates a separate chunk (About.[hash].js) for this route
    // which is lazy-loaded when the route is visited.
    component: () => import('@/pages/leads/sla/AddLeadSlaDetail.vue'),
    meta: {
      layout: 'MainLayout',
    },
  },
  {
    path: '/lead_slas/:id/edit',
    name: 'edit_lead_sla',
    // route level code-splitting
    // this generates a separate chunk (About.[hash].js) for this route
    // which is lazy-loaded when the route is visited.
    component: () => import('@/pages/leads/sla/AddLeadSlaDetail.vue'),
    meta: {
      layout: 'MainLayout',
    },
  },
];

export default lead_slas;
