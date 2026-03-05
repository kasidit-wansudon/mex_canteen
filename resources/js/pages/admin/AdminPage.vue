<template>
  <main class="page auth-page">
    <TopBar :show-logout="false" />
    <section class="panel">
      <h2>今日 Today</h2>
      <h3>{{ today }}</h3>

      <van-tabs v-model:active="activeMeal">
        <van-tab name="lunch" title="午餐 Lunch" />
        <van-tab name="dinner" title="晚餐 Dinner" />
      </van-tabs>

      <van-form @submit="submit">
        <van-cell-group inset>
          <template v-if="savedEntries[activeMeal] && !editing[activeMeal]">
            <van-field :model-value="savedEntries[activeMeal]" type="textarea" rows="8" autosize readonly />
            <van-row :gutter="[12, 0]" style="padding: 8px 12px;">
              <van-col span="12">
                <van-button round block type="primary" @click="startEdit(activeMeal)">
                  ✏️ Edit
                </van-button>
              </van-col>
              <van-col span="12">
                <van-button round block type="danger" plain @click="cancelEntry(activeMeal)">
                  ✕ Cancel
                </van-button>
              </van-col>
            </van-row>
          </template>

          <template v-else>
            <van-field
              v-model="entries[activeMeal]"
              type="textarea"
              :placeholder="'e.g.\n1. Rice with vegetables\n2. Miso soup\n3. Green tea'"
              rows="8"
              autosize
              show-word-limit
              maxlength="300"
            />
          </template>
        </van-cell-group>

        <div style="margin: 16px;" v-if="!savedEntries[activeMeal] || editing[activeMeal]">
          <van-button round block type="primary" native-type="submit">
            Submit
          </van-button>
        </div>
      </van-form>
    </section>
  </main>
</template>

<script setup>
import { reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import { showFailToast, showSuccessToast } from 'vant';
import { login } from '../../api/canteen';
import { setAccessToken } from '../../api/http';
import TopBar from '../../components/TopBar.vue';

const router = useRouter();
const loading = ref(false);
const form = reactive({
    staff_code: '',
    password: '',
    device_name: 'canteen-web',
});

const activeMeal = ref('lunch');
const entries = ref({ lunch: '', dinner: '' });

// Stores confirmed/submitted values
const savedEntries = ref({ lunch: '', dinner: '' });

// Tracks which meal is in edit mode
const editing = ref({ lunch: false, dinner: false });

const today = new Date().toISOString().split('T')[0];

function startEdit(meal) {
    // Copy saved value back into the editable field
    entries.value[meal] = savedEntries.value[meal];
    editing.value[meal] = true;
}

function cancelEntry(meal) {
    // Clear saved entry entirely
    savedEntries.value[meal] = '';
    entries.value[meal] = '';
    editing.value[meal] = false;
}

async function submit() {
    const meal = activeMeal.value;
    const text = entries.value[meal].trim();

    if (!text) {
        showFailToast('Please write something first!');
        return;
    }

    loading.value = true;
    try {
        // Save the entry for this meal
        savedEntries.value[meal] = text;
        editing.value[meal] = false;
        showSuccessToast(`${meal === 'lunch' ? '午餐' : '晚餐'} saved!`);

        // --- Your original login logic below (keep as needed) ---
        // const response = await login(form);
        // ...
    } catch (error) {
        showFailToast(error.response?.data?.message || 'Submit failed');
    } finally {
        loading.value = false;
    }
}
</script>