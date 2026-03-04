<template>
  <main class="page">
    <TopBar @logout="handleLogout" />

    <section class="panel">
      <h2>Order History</h2>

      <div class="filter-grid">
        <van-field v-model="filters.date_from" type="date" label="From" />
        <van-field v-model="filters.date_to" type="date" label="To" />
      </div>

      <van-button type="primary" block @click="loadHistory">Load History</van-button>

      <van-empty v-if="history.length === 0" description="No history" />

      <van-cell-group v-else inset>
        <van-cell
          v-for="item in history"
          :key="item.id"
          :title="`${item.reservation_date} · ${item.meal_type}`"
          :label="`${item.reservation_type} / ${item.status}`"
          :value="`${item.meal_count} meals`"
          is-link
          @click="goDetail(item.id)"
        />
      </van-cell-group>
    </section>

    <BottomNav />
  </main>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import { showFailToast } from 'vant';
import { getReservations, logout } from '../../api/canteen';
import { setAccessToken } from '../../api/http';
import BottomNav from '../../components/BottomNav.vue';
import TopBar from '../../components/TopBar.vue';

const router = useRouter();
const history = ref([]);
const filters = reactive({
  date_from: new Date(Date.now() - 7 * 86400000).toISOString().slice(0, 10),
  date_to: new Date().toISOString().slice(0, 10),
});

async function loadHistory() {
  try {
    const response = await getReservations(filters);
    history.value = response.data.data?.data || [];
  } catch (error) {
    showFailToast(error.response?.data?.message || 'Failed to load history');
  }
}

function goDetail(id) {
  router.push(`/reservation?id=${id}`);
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

onMounted(loadHistory);
</script>
