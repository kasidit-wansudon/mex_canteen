<template>
  <main class="page">
    <TopBar @logout="handleLogout" />

    <section class="panel">
      <h2>Order Dashboard</h2>

      <van-tabs v-model:active="period" @change="loadStats">
        <van-tab name="day" title="Day" />
        <van-tab name="week" title="Week" />
        <van-tab name="month" title="Month" />
      </van-tabs>

      <div class="metrics-grid">
        <article>
          <h3>Reservations</h3>
          <strong>{{ stats.reservations }}</strong>
        </article>
        <article>
          <h3>Collected</h3>
          <strong>{{ stats.collected }}</strong>
        </article>
        <article>
          <h3>No Show</h3>
          <strong>{{ stats.no_shows }}</strong>
        </article>
      </div>

      <van-cell-group inset>
        <van-cell
          v-for="point in stats.chart"
          :key="point.reservation_date"
          :title="point.reservation_date"
          :label="`Reserved: ${point.reserved_count}`"
          :value="`Collected: ${point.collected_count}`"
        />
      </van-cell-group>
    </section>

    <AdminNav />
  </main>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import { showFailToast } from 'vant';
import { adminDashboardStats, logout } from '../../api/canteen';
import { setAccessToken } from '../../api/http';
import AdminNav from '../../components/AdminNav.vue';
import TopBar from '../../components/TopBar.vue';

const router = useRouter();
const period = ref('day');
const stats = ref({
  reservations: 0,
  collected: 0,
  no_shows: 0,
  chart: [],
});

async function loadStats() {
  try {
    const response = await adminDashboardStats({ period: period.value });
    stats.value = response.data.data;
  } catch (error) {
    showFailToast(error.response?.data?.message || 'Failed to load dashboard');
  }
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

onMounted(loadStats);
</script>
