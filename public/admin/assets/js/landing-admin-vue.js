(function () {
    'use strict';

    var root = document.getElementById('landing-admin-app');
    var configElement = document.getElementById('landing-admin-data');

    if (!root || !configElement || typeof Vue === 'undefined') {
        return;
    }

    var config = JSON.parse(configElement.textContent);

    Vue.createApp({
        data: function () {
            return {
                config: config,
                query: '',
                countryFilter: '',
                adsFilter: '',
                currentPage: 1,
                pageSize: 10,
                selectedIds: [],
                savingAdsIds: {},
            };
        },

        computed: {
            isPages: function () {
                return this.config.type === 'pages';
            },

            filteredItems: function () {
                var query = this.query.trim().toLowerCase();
                var countryFilter = this.countryFilter;
                var adsFilter = this.adsFilter;
                var isPages = this.isPages;
                var items = this.config.items;

                if (isPages && countryFilter) {
                    items = items.filter(function (item) {
                        return String(item.targetCountry || '').toUpperCase() === countryFilter;
                    });
                }

                if (isPages && adsFilter) {
                    items = items.filter(function (item) {
                        return String(item.adsStatus || 'off') === adsFilter;
                    });
                }

                if (!query) {
                    return items;
                }

                return items.filter(function (item) {
                    var values = isPages
                        ? [
                            item.title,
                            item.slug,
                            item.currency,
                            item.targetCountry,
                            item.targetCountryName,
                            item.adsStatus,
                            item.status ? 'active' : 'inactive',
                        ]
                        : [item.email, item.totalPurchases, item.totalSpent];

                    return values.some(function (value) {
                        return String(value === null || value === undefined ? '' : value)
                            .toLowerCase()
                            .indexOf(query) !== -1;
                    });
                });
            },

            hasActiveFilters: function () {
                return Boolean(this.query.trim()) || Boolean(this.countryFilter) || Boolean(this.adsFilter);
            },

            totalPages: function () {
                return Math.max(1, Math.ceil(this.filteredItems.length / this.pageSize));
            },

            paginatedItems: function () {
                var start = (this.currentPage - 1) * this.pageSize;

                return this.filteredItems.slice(start, start + this.pageSize);
            },

            pageStart: function () {
                return this.filteredItems.length ? (this.currentPage - 1) * this.pageSize + 1 : 0;
            },

            pageEnd: function () {
                return Math.min(this.currentPage * this.pageSize, this.filteredItems.length);
            },

            visiblePages: function () {
                var start = Math.max(1, this.currentPage - 2);
                var end = Math.min(this.totalPages, start + 4);
                var pages = [];

                start = Math.max(1, end - 4);

                for (var page = start; page <= end; page += 1) {
                    pages.push(page);
                }

                return pages;
            },

            stats: function () {
                if (!this.isPages) {
                    return this.config.stats;
                }

                var items = this.config.items;
                var active = items.filter(function (item) {
                    return item.status;
                }).length;

                return [
                    { label: this.config.labels.totalPages, value: items.length },
                    { label: this.config.labels.activePages, value: active },
                    { label: this.config.labels.inactivePages, value: items.length - active },
                    { label: this.config.labels.totalValue, value: this.formatMoney(items.reduce(function (sum, item) {
                        return sum + Number(item.price || 0);
                    }, 0), this.config.currency) },
                ];
            },

            selectedCount: function () {
                return this.selectedIds.length;
            },

            allPageSelected: function () {
                if (!this.isPages || !this.paginatedItems.length) {
                    return false;
                }

                var self = this;

                return this.paginatedItems.every(function (item) {
                    return self.selectedIds.indexOf(item.id) !== -1;
                });
            },
        },

        watch: {
            query: function () {
                this.currentPage = 1;
            },

            countryFilter: function () {
                this.currentPage = 1;
            },

            adsFilter: function () {
                this.currentPage = 1;
            },

            pageSize: function () {
                this.currentPage = 1;
            },

            totalPages: function (total) {
                if (this.currentPage > total) {
                    this.currentPage = total;
                }
            },
        },

        methods: {
            formatMoney: function (amount, currency) {
                var value = Number(amount || 0);
                var code = currency || 'USD';

                try {
                    return new Intl.NumberFormat(undefined, {
                        style: 'currency',
                        currency: code,
                    }).format(value);
                } catch (error) {
                    return code + ' ' + value.toFixed(2);
                }
            },

            formatDate: function (value) {
                if (!value) {
                    return '—';
                }

                var parsed = new Date(String(value).replace(' ', 'T'));

                if (isNaN(parsed.getTime())) {
                    return String(value);
                }

                return new Intl.DateTimeFormat(undefined, {
                    dateStyle: 'medium',
                    timeStyle: 'short',
                }).format(parsed);
            },

            confirmDuplicate: function (item) {
                var msg = (this.config.labels.confirmDuplicate || 'Duplicate ":name"?').replace(':name', item.title);
                if (!window.confirm(msg)) {
                    return;
                }
                var form = document.createElement('form');
                form.method = 'POST';
                form.action = item.duplicateUrl;
                var token = document.createElement('input');
                token.type = 'hidden';
                token.name = '_token';
                token.value = this.config.csrfToken;
                form.appendChild(token);
                document.body.appendChild(form);
                form.submit();
            },

            confirmDelete: function (item) {
                var label = this.isPages ? item.title : item.email;

                if (window.confirm(this.config.labels.confirmDelete.replace(':name', label))) {
                    window.location.href = item.deleteUrl;
                }
            },

            isSelected: function (id) {
                return this.selectedIds.indexOf(id) !== -1;
            },

            toggleSelect: function (id) {
                var index = this.selectedIds.indexOf(id);

                if (index === -1) {
                    this.selectedIds.push(id);
                    return;
                }

                this.selectedIds.splice(index, 1);
            },

            toggleSelectAll: function () {
                var self = this;

                if (this.allPageSelected) {
                    this.paginatedItems.forEach(function (item) {
                        var index = self.selectedIds.indexOf(item.id);

                        if (index !== -1) {
                            self.selectedIds.splice(index, 1);
                        }
                    });
                    return;
                }

                this.paginatedItems.forEach(function (item) {
                    if (self.selectedIds.indexOf(item.id) === -1) {
                        self.selectedIds.push(item.id);
                    }
                });
            },

            bulkDelete: function () {
                if (!this.isPages || !this.config.bulkDeleteUrl) {
                    return;
                }

                if (!this.selectedIds.length) {
                    window.alert(this.config.labels.noneSelected);
                    return;
                }

                var message = this.config.labels.confirmBulkDelete.replace(':count', String(this.selectedIds.length));

                if (!window.confirm(message)) {
                    return;
                }

                var form = document.createElement('form');
                form.method = 'POST';
                form.action = this.config.bulkDeleteUrl;

                var token = document.createElement('input');
                token.type = 'hidden';
                token.name = '_token';
                token.value = this.config.csrfToken;
                form.appendChild(token);

                this.selectedIds.forEach(function (id) {
                    var input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'ids[]';
                    input.value = id;
                    form.appendChild(input);
                });

                document.body.appendChild(form);
                form.submit();
            },

            goToPage: function (page) {
                if (page < 1 || page > this.totalPages || page === this.currentPage) {
                    return;
                }

                this.currentPage = page;
            },

            isSavingAds: function (id) {
                return Boolean(this.savingAdsIds[id]);
            },

            updateAdsStatus: function (item, event) {
                var self = this;
                var nextStatus = event && event.target ? event.target.value : item.adsStatus;
                var previousStatus = item.adsStatus || 'off';

                if (!item.adsStatusUrl || nextStatus === previousStatus) {
                    return;
                }

                var saving = {};
                Object.keys(this.savingAdsIds).forEach(function (key) {
                    saving[key] = self.savingAdsIds[key];
                });
                saving[item.id] = true;
                this.savingAdsIds = saving;
                item.adsStatus = nextStatus;

                var body = new URLSearchParams();
                body.set('ads_status', nextStatus);
                body.set('_token', this.config.csrfToken);

                fetch(item.adsStatusUrl, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: body,
                    credentials: 'same-origin',
                })
                    .then(function (response) {
                        if (!response.ok) {
                            throw new Error('Request failed');
                        }

                        return response.json();
                    })
                    .then(function (data) {
                        if (!data || !data.success) {
                            throw new Error('Update failed');
                        }

                        item.adsStatus = data.ads_status || nextStatus;
                    })
                    .catch(function () {
                        item.adsStatus = previousStatus;
                        if (event && event.target) {
                            event.target.value = previousStatus;
                        }
                        window.alert(self.config.labels.adsUpdateFailed);
                    })
                    .finally(function () {
                        var nextSaving = {};
                        Object.keys(self.savingAdsIds).forEach(function (key) {
                            if (String(key) !== String(item.id)) {
                                nextSaving[key] = self.savingAdsIds[key];
                            }
                        });
                        self.savingAdsIds = nextSaving;
                    });
            },
        },

        template: [
            '<div class="landing-admin">',
            '    <section class="landing-hero">',
            '        <div>',
            '            <div class="landing-eyebrow">{{ config.section }}</div>',
            '            <h1>{{ config.title }}</h1>',
            '            <p>{{ config.description }}</p>',
            '        </div>',
            '        <div v-if="isPages" class="landing-hero-actions">',
            '            <a :href="config.messagesUrl" class="landing-button landing-button-ghost">',
            '                <i class="fa fa-comments"></i>',
            '                {{ config.labels.messages }}',
            '                <span v-if="config.unreadCount">({{ config.unreadCount }})</span>',
            '            </a>',
            '            <a :href="config.createUrl" class="landing-button landing-button-primary">',
            '                <i class="fa fa-plus"></i>',
            '                {{ config.labels.create }}',
            '            </a>',
            '        </div>',
            '    </section>',
            '    <section class="landing-stats">',
            '        <article v-for="stat in stats" :key="stat.label" class="landing-stat">',
            '            <span class="landing-stat-label">{{ stat.label }}</span>',
            '            <strong class="landing-stat-value">{{ stat.value }}</strong>',
            '        </article>',
            '    </section>',
            '    <section class="landing-panel">',
            '        <header class="landing-toolbar">',
            '            <div class="landing-toolbar-main">',
            '                <h2>{{ config.labels.tableTitle }}</h2>',
            '                <div v-if="isPages && selectedCount" class="landing-bulk-actions">',
            '                    <span class="landing-bulk-count"><strong>{{ selectedCount }}</strong> {{ config.labels.selected }}</span>',
            '                    <button type="button" class="landing-button landing-button-danger" @click="bulkDelete">',
            '                        <i class="fa fa-trash"></i>',
            '                        {{ config.labels.bulkDelete }}',
            '                    </button>',
            '                </div>',
            '            </div>',
            '            <div class="landing-toolbar-filters">',
            '                <label v-if="isPages && config.countries && config.countries.length" class="landing-country-filter">',
            '                    <span class="landing-sr-only">{{ config.labels.filterCountry }}</span>',
            '                    <select v-model="countryFilter" :aria-label="config.labels.filterCountry">',
            '                        <option value="">{{ config.labels.allCountries }}</option>',
            '                        <option v-for="country in config.countries" :key="country.code" :value="country.code">',
            '                            {{ country.label }}',
            '                        </option>',
            '                    </select>',
            '                </label>',
            '                <label v-if="isPages" class="landing-country-filter">',
            '                    <span class="landing-sr-only">{{ config.labels.filterAds }}</span>',
            '                    <select v-model="adsFilter" :aria-label="config.labels.filterAds">',
            '                        <option value="">{{ config.labels.allAdsStatuses }}</option>',
            '                        <option v-for="status in config.adsStatuses" :key="status.value" :value="status.value">',
            '                            {{ status.label }}',
            '                        </option>',
            '                    </select>',
            '                </label>',
            '                <label class="landing-search">',
            '                    <i class="fa fa-search"></i>',
            '                    <input v-model="query" type="search" :placeholder="config.labels.searchPlaceholder" :aria-label="config.labels.searchPlaceholder">',
            '                </label>',
            '            </div>',
            '        </header>',
            '        <div v-if="filteredItems.length" class="landing-table-wrap">',
            '            <table class="landing-table table">',
            '                <thead v-if="isPages">',
            '                    <tr>',
            '                        <th class="landing-col-check">',
            '                            <input type="checkbox" class="landing-checkbox" :checked="allPageSelected" :aria-label="config.labels.selectAll" @change="toggleSelectAll">',
            '                        </th>',
            '                        <th>{{ config.labels.page }}</th>',
            '                        <th>{{ config.labels.price }}</th>',
            '                        <th>{{ config.labels.country }}</th>',
            '                        <th>{{ config.labels.status }}</th>',
            '                        <th>{{ config.labels.ads }}</th>',
            '                        <th>{{ config.labels.publicUrl }}</th>',
            '                        <th>{{ config.labels.sourceUrl }}</th>',
            '                        <th>{{ config.labels.actions }}</th>',
            '                    </tr>',
            '                </thead>',
            '                <thead v-else>',
            '                    <tr>',
            '                        <th>{{ config.labels.customer }}</th>',
            '                        <th>{{ config.labels.purchases }}</th>',
            '                        <th>{{ config.labels.totalSpent }}</th>',
            '                        <th>{{ config.labels.firstPurchase }}</th>',
            '                        <th>{{ config.labels.lastPurchase }}</th>',
            '                        <th>{{ config.labels.actions }}</th>',
            '                    </tr>',
            '                </thead>',
            '                <tbody v-if="isPages">',
            '                    <tr v-for="item in paginatedItems" :key="item.id" :class="{ \'is-selected\': isSelected(item.id) }">',
            '                        <td class="landing-col-check">',
            '                            <input type="checkbox" class="landing-checkbox" :checked="isSelected(item.id)" :aria-label="item.title" @change="toggleSelect(item.id)">',
            '                        </td>',
            '                        <td>',
            '                            <span class="landing-title">{{ item.title }}</span>',
            '                            <span class="landing-subtle">/{{ item.slug }}</span>',
            '                        </td>',
            '                        <td><strong>{{ formatMoney(item.price, item.currency) }}</strong></td>',
            '                        <td>',
            '                            <span v-if="item.targetCountry" class="landing-country-cell">',
            '                                <span class="landing-country-flag" aria-hidden="true">{{ item.targetCountryFlag }}</span>',
            '                                <span>{{ item.targetCountryName || item.targetCountry }}</span>',
            '                            </span>',
            '                            <span v-else class="landing-subtle">—</span>',
            '                        </td>',
            '                        <td>',
            '                            <span :class="[\'landing-badge\', item.status ? \'landing-badge-success\' : \'landing-badge-muted\']" :title="item.status ? config.labels.activeHint : config.labels.inactiveHint">',
            '                                {{ item.status ? config.labels.active : config.labels.inactive }}',
            '                            </span>',
            '                            <span v-if="!item.status && item.adsStatus === \'published\'" class="landing-subtle text-danger" :title="config.labels.inactiveHint"><i class="fa fa-exclamation-triangle"></i> {{ config.labels.inactiveOnAds }}</span>',
            '                        </td>',
            '                        <td>',
            '                            <label class="landing-ads-status" :class="[\'is-\' + (item.adsStatus || \'off\'), { \'is-saving\': isSavingAds(item.id) }]">',
            '                                <span class="landing-sr-only">{{ config.labels.ads }}</span>',
            '                                <select',
            '                                    :value="item.adsStatus || \'off\'"',
            '                                    :disabled="isSavingAds(item.id)"',
            '                                    :aria-label="config.labels.ads + \': \' + item.title"',
            '                                    @change="updateAdsStatus(item, $event)"',
            '                                >',
            '                                    <option v-for="status in config.adsStatuses" :key="status.value" :value="status.value">',
            '                                        {{ status.label }}',
            '                                    </option>',
            '                                </select>',
            '                            </label>',
            '                        </td>',
            '                        <td>',
            '                            <a :href="item.publicUrl" target="_blank" rel="noopener" class="landing-icon-button" :class="{ \'is-disabled\': !item.status }" :title="item.status ? config.labels.publicUrl : config.labels.inactiveHint">',
            '                                <i class="fa fa-external-link"></i>',
            '                            </a>',
            '                        </td>',
            '                        <td>',
            '                            <a',
            '                                v-if="item.sourceUrl"',
            '                                :href="item.sourceUrl"',
            '                                target="_blank"',
            '                                rel="noopener noreferrer"',
            '                                class="landing-icon-button landing-icon-button-source"',
            '                                :title="config.labels.sourceUrl"',
            '                            >',
            '                                <i class="fa fa-shopping-bag"></i>',
            '                            </a>',
            '                            <span v-else class="landing-icon-button is-disabled" :title="config.labels.noSourceUrl" aria-disabled="true">',
            '                                <i class="fa fa-shopping-bag"></i>',
            '                            </span>',
            '                        </td>',
            '                        <td>',
            '                            <div class="landing-actions">',
            '                                <a :href="item.editUrl" class="landing-icon-button" :title="config.labels.edit"><i class="fa fa-pencil"></i></a>',
            '                                <a :href="item.analyticsUrl" class="landing-icon-button" :title="config.labels.analytics"><i class="fa fa-bar-chart"></i></a>',
            '                                <button type="button" class="landing-icon-button" :title="config.labels.duplicate" @click="confirmDuplicate(item)"><i class="fa fa-copy"></i></button>',
            '                                <button type="button" class="landing-icon-button landing-icon-button-danger" :title="config.labels.delete" @click="confirmDelete(item)">',
            '                                    <i class="fa fa-trash"></i>',
            '                                </button>',
            '                            </div>',
            '                        </td>',
            '                    </tr>',
            '                </tbody>',
            '                <tbody v-else>',
            '                    <tr v-for="item in paginatedItems" :key="item.id">',
            '                        <td>',
            '                            <span class="landing-title">{{ item.email }}</span>',
            '                            <span class="landing-subtle">#{{ item.id }}</span>',
            '                        </td>',
            '                        <td><span class="landing-badge landing-badge-success">{{ item.totalPurchases }}</span></td>',
            '                        <td><strong>{{ formatMoney(item.totalSpent, config.currency) }}</strong></td>',
            '                        <td>{{ formatDate(item.firstPurchaseAt) }}</td>',
            '                        <td>{{ formatDate(item.lastPurchaseAt) }}</td>',
            '                        <td>',
            '                            <button type="button" class="landing-icon-button landing-icon-button-danger" :title="config.labels.delete" @click="confirmDelete(item)">',
            '                                <i class="fa fa-trash"></i>',
            '                            </button>',
            '                        </td>',
            '                    </tr>',
            '                </tbody>',
            '            </table>',
            '        </div>',
            '        <footer v-if="filteredItems.length" class="landing-pagination">',
            '            <div class="landing-pagination-summary">',
            '                {{ config.labels.showing }} <strong>{{ pageStart }}–{{ pageEnd }}</strong>',
            '                {{ config.labels.of }} <strong>{{ filteredItems.length }}</strong>',
            '            </div>',
            '            <div class="landing-pagination-controls">',
            '                <label class="landing-page-size">',
            '                    <span>{{ config.labels.rowsPerPage }}</span>',
            '                    <select v-model.number="pageSize" aria-label="Rows per page">',
            '                        <option :value="5">5</option>',
            '                        <option :value="10">10</option>',
            '                        <option :value="25">25</option>',
            '                        <option :value="50">50</option>',
            '                    </select>',
            '                </label>',
            '                <nav v-if="totalPages > 1" class="landing-page-buttons" :aria-label="config.labels.pagination">',
            '                    <button type="button" class="landing-page-button landing-page-button-arrow" :disabled="currentPage === 1" :aria-label="config.labels.previous" @click="goToPage(currentPage - 1)">',
            '                        <i class="fa fa-angle-left"></i>',
            '                    </button>',
            '                    <button v-for="page in visiblePages" :key="page" type="button" :class="[\'landing-page-button\', { \'is-active\': page === currentPage }]" :aria-current="page === currentPage ? \'page\' : null" @click="goToPage(page)">',
            '                        {{ page }}',
            '                    </button>',
            '                    <button type="button" class="landing-page-button landing-page-button-arrow" :disabled="currentPage === totalPages" :aria-label="config.labels.next" @click="goToPage(currentPage + 1)">',
            '                        <i class="fa fa-angle-right"></i>',
            '                    </button>',
            '                </nav>',
            '            </div>',
            '        </footer>',
            '        <div v-else class="landing-empty">',
            '            <i class="fa fa-inbox"></i>',
            '            <strong>{{ hasActiveFilters ? config.labels.noResults : config.emptyMessage }}</strong>',
            '        </div>',
            '    </section>',
            '</div>',
        ].join('\n'),
    }).mount(root);
})();
