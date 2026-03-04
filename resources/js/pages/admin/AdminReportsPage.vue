<template>
  <main class="page">
    <TopBar @logout="handleLogout" />

    <section class="panel">
      <h2>Data Center & Reports</h2>

      <van-tabs v-model:active="activeTab">
        <van-tab name="daily" title="Daily" />
        <van-tab name="monthly" title="Monthly" />
        <van-tab name="weekly" title="7-Day" />
      </van-tabs>

      <div class="filter-grid">
        <van-field v-model="filters.date_from" type="date" label="From" />
        <van-field v-model="filters.date_to" type="date" label="To" />
        <van-field v-model="filters.staff_type" label="Staff Type" placeholder="jumbo / latam" />
        <van-field v-model="filters.staff_code" label="Staff Code" />
      </div>

      <van-button block type="primary" @click="loadReport">Load Report</van-button>
      <van-button v-if="activeTab === 'daily'" block plain type="primary" @click="exportCsv">Export CSV</van-button>

      <van-cell-group v-if="activeTab === 'daily'" inset>
        <van-cell
          v-for="row in rows"
          :key="row.id"
          :title="`${row.reservation_date} · ${row.staff_code || row.visitor_name}`"
          :label="`${row.status_label} / ${row.meal_type}`"
          :value="`${row.meal_count}`"
        />
      </van-cell-group>

      <van-cell-group v-if="activeTab === 'monthly'" inset>
        <van-cell
          v-for="row in rows"
          :key="row.id"
          :title="`${row.staff_code} - ${row.staff_name}`"
          :label="`Reserve: ${row.reservation_count}, Collected: ${row.actual_collected}`"
          :value="`Cost: ${row.cost_calculation}`"
        />
      </van-cell-group>

      <div v-if="activeTab === 'weekly'" class="weekly-grid">
        <article v-for="row in rows" :key="row.staff_code" class="weekly-card">
          <h3>{{ row.staff_code }} - {{ row.staff_name }}</h3>
          <ul>
            <li v-for="(value, day) in row.grid" :key="day">{{ day }}: {{ value }}</li>
          </ul>
        </article>
      </div>
    </section>

    <AdminNav />
  </main>
</template>

<script setup>
import { reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import { showFailToast } from 'vant';
import { adminDailyReport, adminMonthlyReport, adminWeeklyGrid, logout } from '../../api/canteen';
import { setAccessToken } from '../../api/http';
import AdminNav from '../../components/AdminNav.vue';
import TopBar from '../../components/TopBar.vue';

const router = useRouter();
const activeTab = ref('daily');
const rows = ref([]);

const filters = reactive({
  date_from: new Date(Date.now() - 7 * 86400000).toISOString().slice(0, 10),
  date_to: new Date().toISOString().slice(0, 10),
  staff_type: '',
  staff_code: '',
});

async function loadReport() {
  try {
    if (activeTab.value === 'daily') {
      const response = await adminDailyReport(filters);
      rows.value = response.data.data || [];
      return;
    }

    if (activeTab.value === 'monthly') {
      const response = await adminMonthlyReport(filters);
      rows.value = response.data.data || [];
      return;
    }

    const response = await adminWeeklyGrid({
      week_start: filters.date_from,
      staff_type: filters.staff_type || undefined,
    });
    rows.value = response.data.data || [];
  } catch (error) {
    showFailToast(error.response?.data?.message || 'Load report failed');
  }
}

function exportCsv() {
  const query = new URLSearchParams({ ...filters, export: 'csv' }).toString();
  window.open(`/api/admin/reports/daily?${query}`, '_blank');
}

async function handleLogout() {
  try {
    await logout();
  } catch (error) {
    // Ignore logout API error and clear token locally.
  } finally {
    setAccessToken(null);
    localStorage.removeItem('canteen_auth');
    router.push('/admin');
  }
}
</script>
