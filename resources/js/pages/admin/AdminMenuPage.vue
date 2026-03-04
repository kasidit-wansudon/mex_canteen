<template>
  <main class="page">
    <TopBar @logout="handleLogout" />
    <section class="panel">
      <h2>Meal Plan Management</h2>

      <van-form @submit="saveMealPlan">
        <van-field v-model="form.meal_date" type="date" label="Date" required />
        <van-field name="mealType" label="Meal Type">
          <template #input>
            <van-radio-group v-model="form.meal_type" direction="horizontal">
              <van-radio name="lunch">Lunch</van-radio>
              <van-radio name="dinner">Dinner</van-radio>
            </van-radio-group>
          </template>
        </van-field>
        <van-field v-model="form.menu_item_1" label="Menu 1" required />
        <van-field v-model="form.menu_item_2" label="Menu 2" />
        <van-field v-model="form.menu_item_3" label="Menu 3" />
        <van-field v-model="form.reservation_open_at" type="datetime-local" label="Open" />
        <van-field v-model="form.reservation_close_at" type="datetime-local" label="Close" />
        <van-field v-model="form.collection_start_at" type="datetime-local" label="Collect Start" />
        <van-field v-model="form.collection_end_at" type="datetime-local" label="Collect End" />

        <div class="form-action">
          <van-button block type="primary" native-type="submit" :loading="loading">Save</van-button>
        </div>
      </van-form>
    </section>

    <AdminNav />
  </main>
</template>

<script setup>
import { reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import { showFailToast, showSuccessToast } from 'vant';
import { adminSaveMealPlan, logout } from '../../api/canteen';
import { setAccessToken } from '../../api/http';
import AdminNav from '../../components/AdminNav.vue';
import TopBar from '../../components/TopBar.vue';

const router = useRouter();
const loading = ref(false);

const form = reactive({
  meal_date: new Date().toISOString().slice(0, 10),
  meal_type: 'lunch',
  menu_item_1: '',
  menu_item_2: '',
  menu_item_3: '',
  reservation_open_at: '',
  reservation_close_at: '',
  collection_start_at: '',
  collection_end_at: '',
  status: 'published',
});

async function saveMealPlan() {
  loading.value = true;

  try {
    const response = await adminSaveMealPlan(form);
    showSuccessToast(response.data.message || 'Saved');
  } catch (error) {
    showFailToast(error.response?.data?.message || 'Save failed');
  } finally {
    loading.value = false;
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
</script>
