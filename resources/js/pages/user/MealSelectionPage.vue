<template>
  <main class="page">
    <TopBar @logout="handleLogout" />
    <section class="panel">
      <h2>今日 Today</h2>
      <h3>{{ today }}</h3>

      <van-tabs v-model:active="mealType">
        <van-tab name="lunch" title="午餐 Lunch" />
        <van-tab name="dinner" title="晚餐 Dinner" />
      </van-tabs>

      <van-empty v-if="!selectedPlan" description="No meal plan" />

      <div v-else class="menu-card">
        <van-field
          :model-value="selectedPlan.menu"
          type="textarea"
          rows="5"
          autosize
          readonly
        />

        <!-- ── Confirmed reservation summary ── -->
        <template v-if="savedReservations[mealType] && !editing">
          <div class="summary-box">
            <van-icon name="success" color="#07c160" size="32" />
            <div class="summary-info">
              <p class="summary-type">{{ summaryLabel(savedReservations[mealType]) }}</p>
              <p v-if="savedReservations[mealType].visitor_count" class="summary-detail">
                访客人数 Visitors: {{ savedReservations[mealType].visitor_count }}
              </p>
              <p v-if="savedReservations[mealType].pickup_for_staff_code" class="summary-detail">
                员工号 Staff code: {{ savedReservations[mealType].pickup_for_staff_code }}
              </p>
            </div>
            <van-button size="small" round type="primary" @click="startEdit">Edit</van-button>
            <van-button size="small" round type="danger" @click="deleteReservation">Cancel</van-button>
          </div>
        </template>

        <!-- ── Reserve button (first time) ── -->
        <template v-else-if="!showReservationForm">
          <div style="margin: 16px;">
            <van-button round block type="primary" @click="showReservationForm = true">
              预约 Reserve
            </van-button>
          </div>
        </template>

        <!-- ── Reservation form ── -->
        <transition name="slide-down">
          <div v-if="showReservationForm" class="reservation-section">
            <van-divider>预约类型 Reservation Type</van-divider>

            <div class="type-grid three-col">
              <div
                v-for="t in mainTypes"
                :key="t.value"
                class="type-card"
                :class="{ selected: mainType === t.value }"
                @click="selectMainType(t.value)"
              >
                <span class="type-zh">{{ t.zh }}</span>
                <span class="type-en">{{ t.en }}</span>
              </div>
            </div>

            <template v-if="mainType === 'invitation'">
              <van-divider style="margin-top: 16px;">选择方式 Select</van-divider>
              <div class="type-grid two-col">
                <div
                  v-for="t in invitationSubTypes"
                  :key="t.value"
                  class="type-card"
                  :class="{ selected: form.reservation_type === t.value }"
                  @click="form.reservation_type = t.value"
                >
                  <span class="type-zh">{{ t.zh }}</span>
                  <span class="type-en">{{ t.en }}</span>
                </div>
              </div>
              <van-cell-group inset style="margin-top: 12px;">
                <van-field v-model.number="form.visitor_count" type="digit" label="访客人数 Visitors" placeholder="Qty" />
              </van-cell-group>
            </template>

            <template v-if="mainType === 'pickup'">
              <van-divider style="margin-top: 16px;">选择方式 Select</van-divider>
              <div class="type-grid two-col">
                <div
                  v-for="t in pickupSubTypes"
                  :key="t.value"
                  class="type-card"
                  :class="{ selected: form.reservation_type === t.value }"
                  @click="form.reservation_type = t.value"
                >
                  <span class="type-zh">{{ t.zh }}</span>
                  <span class="type-en">{{ t.en }}</span>
                </div>
              </div>
              <van-cell-group inset style="margin-top: 12px;">
                <van-field v-model="form.pickup_for_staff_code" label="员工号 Staff code" placeholder="XX" />
              </van-cell-group>
            </template>

            <van-row :gutter="[12, 0]" style="margin: 16px;">
              <!-- Cancel edit: only shown when editing an existing reservation -->
              <van-col v-if="editing" span="12">
                <van-button round block plain type="danger" @click="cancelEdit">
                  ✕ Cancel
                </van-button>
              </van-col>
              <van-col :span="editing ? 12 : 24">
                <van-button
                  round block type="primary"
                  :loading="loading"
                  :disabled="!canConfirm"
                  @click="submitReservation"
                >
                  确认 Confirm
                </van-button>
              </van-col>
            </van-row>
          </div>
        </transition>
      </div>
    </section>

    <BottomNav />
  </main>
</template>

<script setup>
import { computed, onMounted, reactive, ref, watch } from 'vue';
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
const showReservationForm = ref(false);
const mainType = ref(null);
const editing = ref(false);
const today = new Date().toISOString().slice(0, 10);

const form = reactive({
  reservation_type: '',
  visitor_count: 1,
  pickup_for_staff_code: '',
});

// Saved reservation per meal type
const savedReservations = ref({ lunch: null, dinner: null });

const mainTypes = [
  { value: 'self',       zh: '自己用餐', en: 'Self Order' },
  { value: 'invitation', zh: '带人用餐', en: 'Invitation' },
  { value: 'pickup',     zh: '替人取餐', en: 'Pick up' },
];

const invitationSubTypes = [
  { value: 'self+invitation', zh: '自己就餐 + 带访客', en: 'Self + Invitation' },
  { value: 'only_invitation', zh: '仅访客用餐',        en: 'Only Invitation' },
];

const pickupSubTypes = [
  { value: 'self+pickup', zh: '自己就餐 + 替人取餐', en: 'Self + Pick up' },
  { value: 'only_pickup', zh: '仅替人取餐',          en: 'Only Pick up' },
];

const canConfirm = computed(() => {
  if (!mainType.value) return false;
  if (mainType.value === 'self') return true;
  if (mainType.value === 'invitation') return !!form.reservation_type && form.visitor_count > 0;
  if (mainType.value === 'pickup') return !!form.reservation_type && !!form.pickup_for_staff_code;
  return false;
});

const allTypes = [...mainTypes, ...invitationSubTypes, ...pickupSubTypes];

function summaryLabel(reservation) {
  const match = allTypes.find((t) => t.value === reservation.reservation_type);
  return match ? `${match.zh} · ${match.en}` : reservation.reservation_type;
}

function selectMainType(value) {
  mainType.value = value;
  form.reservation_type = value === 'self' ? 'self' : '';
  form.visitor_count = 1;
  form.pickup_for_staff_code = '';
}

function startEdit() {
  const saved = savedReservations.value[mealType.value];
  // Restore form state from saved reservation
  const type = saved.reservation_type;
  if (type === 'self') mainType.value = 'self';
  else if (['self+invitation', 'only_invitation'].includes(type)) mainType.value = 'invitation';
  else if (['self+pickup', 'only_pickup'].includes(type)) mainType.value = 'pickup';

  form.reservation_type = type;
  form.visitor_count = saved.visitor_count || 1;
  form.pickup_for_staff_code = saved.pickup_for_staff_code || '';

  editing.value = true;
  showReservationForm.value = true;
}

function deleteReservation() {
  savedReservations.value[mealType.value] = null;

}

function cancelEdit() {
  editing.value = false;
  showReservationForm.value = false;
  resetForm();
}

function resetForm() {
  mainType.value = null;
  form.reservation_type = '';
  form.visitor_count = 1;
  form.pickup_for_staff_code = '';
}

watch(mealType, () => {
  showReservationForm.value = false;
  editing.value = false;
  resetForm();
});

const selectedPlan = computed(() =>
  mealPlans.value.find((item) => item.meal_type === mealType.value)
);

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
      visitor_count: mainType.value === 'invitation' ? form.visitor_count : null,
      pickup_for_staff_code: mainType.value === 'pickup' ? form.pickup_for_staff_code : null,
    };

    // const response = await createReservation(payload);
    // showSuccessToast(response.data.message || 'Created');

    // Mock: save locally
    savedReservations.value[mealType.value] = { ...payload };
    showSuccessToast(editing.value ? 'Updated!' : 'Reserved!');

    editing.value = false;
    showReservationForm.value = false;
    resetForm();
  } catch (error) {
    showFailToast(error.response?.data?.message || 'Failed');
  } finally {
    loading.value = false;
  }
}

async function handleLogout() {
  try { await logout(); } catch {}
  finally {
    setAccessToken(null);
    localStorage.removeItem('canteen_auth');
    router.push('/admin');
  }
}

async function loadMealPlans() {
  mealPlans.value = [
    {
      id: 1,
      meal_type: 'lunch',
      date: queryDate.value,
      menu: '1. Steamed rice\n2. Kung Pao chicken\n3. Tomato egg soup\n4. Seasonal vegetables',
    },
    {
      id: 2,
      meal_type: 'dinner',
      date: queryDate.value,
      menu: '1. Fried noodles\n2. Sweet & sour pork\n3. Mapo tofu\n4. Green tea',
    },
  ];
}

onMounted(loadMealPlans);
</script>

<style scoped>
.menu-card {
  background: #fff;
  border-radius: 12px;
  margin: 12px 16px;
  overflow: hidden;
}

.summary-box {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 16px;
  border-top: 1px solid #f0f0f0;
}

.summary-info {
  flex: 1;
}

.summary-type {
  font-size: 14px;
  font-weight: 600;
  color: #333;
}

.summary-detail {
  font-size: 12px;
  color: #999;
  margin-top: 2px;
}

.reservation-section { padding-bottom: 8px; }

.type-grid { display: grid; gap: 10px; padding: 0 16px; }
.three-col { grid-template-columns: repeat(3, 1fr); }
.two-col   { grid-template-columns: repeat(2, 1fr); }

.type-card {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 4px;
  padding: 12px 6px;
  border: 1.5px solid #eee;
  border-radius: 10px;
  cursor: pointer;
  transition: all 0.18s;
  text-align: center;
}

.type-card.selected { border-color: #1989fa; background: #e8f3ff; }
.type-zh { font-size: 13px; font-weight: 600; color: #333; }
.type-en { font-size: 11px; color: #999; }
.type-card.selected .type-zh,
.type-card.selected .type-en { color: #1989fa; }

.slide-down-enter-active,
.slide-down-leave-active { transition: all 0.3s ease; }
.slide-down-enter-from,
.slide-down-leave-to { opacity: 0; transform: translateY(-10px); }
</style>