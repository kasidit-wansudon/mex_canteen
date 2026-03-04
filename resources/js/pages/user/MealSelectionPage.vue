<template>
  <main class="page">
    <TopBar @logout="handleLogout" />
    <section class="panel">
      <h2>{{ $t('reservation.title') }}</h2>
      <van-field v-model="queryDate" type="date" :label="$t('common.date')" @change="loadMealPlans" />

      <van-tabs v-model:active="mealType">
        <van-tab name="lunch" title="Lunch" />
        <van-tab name="dinner" title="Dinner" />
      </van-tabs>

      <van-empty v-if="!selectedPlan" description="No meal plan" />

      <div v-else class="menu-card">
        <div class="menu-date">{{ selectedPlan.meal_date }} · {{ selectedPlan.meal_type }}</div>
        <ul>
          <li v-for="item in selectedPlan.menu_items" :key="item">{{ item }}</li>
        </ul>
        <small>Reservation deadline: {{ selectedPlan.reservation_close_at || '-' }}</small>
      </div>

      <van-form @submit="submitReservation">
        <van-field name="reservationType" :label="$t('reservation.type')">
          <template #input>
            <van-radio-group v-model="form.reservation_type" direction="vertical">
              <van-radio name="self">Self</van-radio>
              <van-radio name="self_invitation">Self + Invitation</van-radio>
              <van-radio name="self_pickup">Self + Pick up</van-radio>
              <van-radio name="invitation_only">Only Invitation</van-radio>
              <van-radio name="pickup_only">Only Pick up</van-radio>
            </van-radio-group>
          </template>
        </van-field>

        <van-field
          v-if="['self_invitation', 'invitation_only'].includes(form.reservation_type)"
          v-model.number="form.visitor_count"
          type="number"
          :label="$t('reservation.visitor_count')"
          min="1"
        />

        <van-field
          v-if="['self_pickup', 'pickup_only'].includes(form.reservation_type)"
          v-model="form.pickup_for_staff_code"
          :label="$t('reservation.pickup_code')"
        />

        <van-field v-model="form.remark" type="textarea" rows="2" :label="$t('reservation.remark')" />

        <div class="form-action">
          <van-button block type="primary" native-type="submit" :loading="loading">
            {{ $t('reservation.create') }}
          </van-button>
        </div>
      </van-form>
    </section>

    <BottomNav />
  </main>
</template>

<script setup>
import { computed, onMounted, reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import { showFailToast, showSuccessToast } from 'vant';
import { createReservation, getDailyMealPlans, logout } from '../../api/canteen';
import { setAccessToken } from '../../api/http';
import BottomNav from '../../components/BottomNav.vue';
import TopBar from '../../components/TopBar.vue';

const router = useRouter();
const loading = ref(false);
const queryDate = ref(new Date().toISOString().slice(0, 10));
const mealType = ref('lunch');
const mealPlans = ref([]);

const form = reactive({
  reservation_type: 'self',
  visitor_count: 1,
  pickup_for_staff_code: '',
  remark: '',
});

const selectedPlan = computed(() => mealPlans.value.find((item) => item.meal_type === mealType.value));

async function loadMealPlans() {
  try {
    const response = await getDailyMealPlans(queryDate.value);
    mealPlans.value = response.data.data || [];
  } catch (error) {
    showFailToast(error.response?.data?.message || 'Failed to load meal plans');
  }
}

async function submitReservation() {
  if (!selectedPlan.value) {
    showFailToast('No meal plan selected');
    return;
  }

  loading.value = true;

  try {
    const payload = {
      meal_plan_id: selectedPlan.value.id,
      reservation_type: form.reservation_type,
      visitor_count: form.visitor_count,
      pickup_for_staff_code: form.pickup_for_staff_code,
      remark: form.remark,
    };

    const response = await createReservation(payload);
    showSuccessToast(response.data.message || 'Created');
    router.push(`/reservation?id=${response.data.data.id}`);
  } catch (error) {
    showFailToast(error.response?.data?.message || 'Create reservation failed');
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

onMounted(loadMealPlans);
</script>
