<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <title>Laravel Health Monitor</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
    <link href="https://fonts.bunny.net/css?family=figtree:300,400,500,600" rel="stylesheet"/>
    <script src="https://cdn.jsdelivr.net/npm/vue@3.2.47/dist/vue.global.min.js"></script>
    <style>
        :root {
            --bg: #0f172a;
            --card-bg: #1e293b;
            --text: #f8fafc;
            --green: #22c55e;
            --red: #ef4444;
            --gray: #94a3b8;
            --border: #334155;
            --shadow: rgba(0, 0, 0, 0.25);
        }
        * {
            box-sizing: border-box;
        }
        body {
            background: var(--bg);
            font-family: Figtree, 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text);
            margin: 0;
            padding: 1rem 0;
            line-height: 1.4;
        }
        .container {
            max-width: 1400px;
            padding-left: 1rem;
            padding-right: 1rem;
            margin-left: auto;
            margin-right: auto;
        }
        .card {
            background-color: var(--card-bg);
            border-radius: 14px;
            padding: 0 0 2.5rem 0;
            box-shadow: 0 4px 8px var(--shadow);
            border: 1px solid var(--border);
        }
        h1 {
            text-align: left;
            font-weight: 600;
            font-size: 1.5rem;
            margin-bottom: 2.5rem;
            padding-bottom: 1rem;
            user-select: none;
            border-bottom: 1px solid var(--border);
        }
        .status-row {
            display: grid;
            grid-template-columns: 1fr auto auto;
            align-items: center;
            padding: 0.8rem 1rem;
            border-bottom: 1px solid var(--border);
            gap: 1.25rem;
            transition: background-color 0.2s ease;
        }
        .status-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }
        .status-row:hover {
            background-color: rgba(255 255 255 / 0.05);
        }
        .label {
            color: var(--gray);
            font-size: 0.9rem;
            font-weight: 500;
            word-break: break-word;
            display: flex;
            flex-direction: column;
        }
        .label strong {
            color: var(--text);
            margin-bottom: 0.25rem;
            user-select: text;
        }
        .driver-info {
            font-size: 0.83rem;
            color: var(--gray);
            font-style: italic;
            user-select: text;
        }
        .status {
            display: flex;
            align-items: center;
            min-width: 120px;
            justify-content: flex-start;
            text-align: right;
            user-select: none;
        }
        .status svg {
            width: 20px;
            height: 20px;
            margin-right: 10px;
            flex-shrink: 0;
        }
        .active {
            color: var(--green);
        }
        .inactive {
            color: var(--red);
        }
        .response-time {
            color: var(--gray);
            font-size: 0.9rem;
            min-width: 60px;
            text-align: right;
            font-family: 'Courier New', Courier, monospace;
            user-select: none;
        }
        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
        }
        .section-title {
            font-size: 1rem;
            font-weight: 600;
            color: var(--text);
            padding: 1rem 0.5rem;
            letter-spacing: 0.05rem;
            background-color: #374151;
            border-radius: 14px 14px 0 0;
        }
        .last-updated {
            text-align: right;
            font-size: 0.8rem;
            color: var(--gray);
            margin-top: 1rem;
        }
        @media (max-width: 480px) {
            .status-row {
                grid-template-columns: 1fr auto;
                grid-template-rows: auto auto;
                gap: 0.5rem 1rem;
                padding: 1rem 1rem;
            }
            .response-time {
                text-align: left;
            }
            .status {
                justify-content: flex-start;
                min-width: auto;
            }
            .grid-2 {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
<div id="app" class="container" role="main">
    <h1>Services Health Monitor{{ config('app.name') ? ' - ' . config('app.name') : '' }}</h1>
    <div class="grid-2">
        <div class="card" role="region" aria-label="Services Health Monitor">
            <div>
                <div class="section-title">Application Services</div>
                <div v-for="check in appChecks" :key="check.name" class="status-row">
                    <div class="label">
                        <strong>@{{ check.name }}</strong>
                        <span v-if="check.driver" class="driver-info">Driver: @{{ check.driver }}</span>
                    </div>
                    <div class="status">
                        <template v-if="check.status">
                            <svg xmlns="http://www.w3.org/2000/svg" class="active" fill="none"
                                 viewBox="0 0 24 24"
                                 stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Active
                        </template>
                        <template v-else>
                            <svg xmlns="http://www.w3.org/2000/svg" class="inactive" fill="none"
                                 viewBox="0 0 24 24"
                                 stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            <span @click="showError(check.exception || 'Unknown error')"
                                  style="cursor: pointer">Inactive</span>
                        </template>
                    </div>
                    <div class="response-time">
                        @{{ check.responseTime ? check.responseTime.toFixed(2) + ' ms' : '-' }}
                    </div>
                </div>
            </div>
        </div>
        <div class="card" role="region" aria-label="Services Health Monitor">
            <div>
                <div class="section-title">Third Party Services</div>
                <div v-for="check in thirdPartyChecks" :key="check.name" class="status-row">
                    <div class="label">
                        <strong>@{{ check.name }}</strong>
                        <span v-if="check.driver" class="driver-info">Driver: @{{ check.driver }}</span>
                    </div>
                    <div class="status">
                        <template v-if="check.status">
                            <svg xmlns="http://www.w3.org/2000/svg" class="active" fill="none"
                                 viewBox="0 0 24 24"
                                 stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Active
                        </template>
                        <template v-else>
                            <svg xmlns="http://www.w3.org/2000/svg" class="inactive" fill="none"
                                 viewBox="0 0 24 24"
                                 stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            <span @click="showError(check.exception || 'Unknown error')"
                                  style="cursor: pointer">Inactive</span>
                        </template>
                    </div>
                    <div class="response-time">
                        @{{ check.responseTime ? check.responseTime.toFixed(2) + ' ms' : '-' }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="last-updated" v-if="lastUpdated">
        Last updated: @{{ lastUpdated }}
    </div>
</div>
<div id="error-modal"
     style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:1000; justify-content:center; align-items:center;">
    <div
        style="background: var(--card-bg); padding: 1.5rem 2rem; border-radius: 12px; max-width: 500px; color: var(--text); box-shadow: 0 4px 10px var(--shadow);">
        <h3 style="margin-top: 0;">Error Details</h3>
        <p id="modal-message" style="color: var(--gray); font-size: 0.95rem; white-space: pre-wrap;"></p>
        <button onclick="document.getElementById('error-modal').style.display='none'"
                style="margin-top: 1rem; background: var(--red); color: white; border: none; padding: 0.5rem 1rem; border-radius: 8px; cursor: pointer;">
            Close
        </button>
    </div>
</div>
<script>
    const { createApp, ref, computed, onMounted } = Vue;

    createApp({
        setup() {
            const checks = ref([]);
            const lastUpdated = ref(null);
            const loading = ref(true);
            const error = ref(null);
            const fetchData = async () => {
                try {
                    const response = await fetch('/api-service-health');
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    const data = await response.json();
                    checks.value = data.checks;
                    lastUpdated.value = new Date().toLocaleTimeString();
                    error.value = null;
                } catch (err) {
                    error.value = err.message;
                    console.error('Error fetching data:', err);
                } finally {
                    loading.value = false;
                }
            };

            const appChecks = computed(() => {
                return checks.value.filter(check => !check.category || check.category === 'app');
            });

            const thirdPartyChecks = computed(() => {
                return checks.value.filter(check => check.category === 'third-party');
            });

            const showError = (message) => {
                document.getElementById('modal-message').innerText = message;
                document.getElementById('error-modal').style.display = 'flex';
            };

            onMounted(() => {
                fetchData();
                setInterval(fetchData, 3000);
            });

            return {
                checks,
                appChecks,
                thirdPartyChecks,
                lastUpdated,
                loading,
                error,
                showError
            };
        }
    }).mount('#app');
</script>
</body>
</html>
