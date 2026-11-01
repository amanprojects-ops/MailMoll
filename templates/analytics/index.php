<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h4 class="fw-bold mb-1">Analytics & Delivery Performance</h4>
        <p class="text-muted small mb-0">Visual intelligence on SMTP throughput, provider distribution, and response rates.</p>
    </div>
</div>

<div class="alert alert-light border py-2 small mb-4 d-flex align-items-center">
    <i class="bi bi-info-circle-fill text-primary me-2"></i>
    <span><strong>Diagnostic Notice:</strong> Metrics display confirmed SMTP acceptance (250 OK) at the recipient Mail Transfer Agent (MTA). True inbox placement depends on destination domain spam filters and sender IP reputation.</span>
</div>

<div class="row g-4 mb-4">
    <!-- 14-Day Delivery Trends -->
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header fw-bold small">
                <i class="bi bi-graph-up text-primary me-1"></i> Daily Dispatch Activity (Last 14 Days)
            </div>
            <div class="card-body">
                <div style="height: 300px;">
                    <canvas id="dailyActivityChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- SMTP Utilization Breakdown -->
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header fw-bold small">
                <i class="bi bi-pie-chart-fill text-primary me-1"></i> SMTP Node Utilization
            </div>
            <div class="card-body">
                <div style="height: 300px;">
                    <canvas id="smtpShareChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Campaign Performance Comparison -->
    <div class="col-12">
        <div class="card">
            <div class="card-header fw-bold small">
                <i class="bi bi-bar-chart-line-fill text-primary me-1"></i> Recent Campaign Performance Comparison
            </div>
            <div class="card-body">
                <div style="height: 280px;">
                    <canvas id="campaignCompareChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    fetch(window.MAILMOLL_URL + '/api/analytics/data')
    .then(res => res.json())
    .then(data => {
        // 1. Daily Activity Line Chart
        const ctxDaily = document.getElementById('dailyActivityChart').getContext('2d');
        new Chart(ctxDaily, {
            type: 'line',
            data: {
                labels: data.daily.labels,
                datasets: [
                    {
                        label: 'SMTP Accepted',
                        data: data.daily.accepted,
                        borderColor: '#10B981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        fill: true,
                        tension: 0.3
                    },
                    {
                        label: 'SMTP Rejected / Failed',
                        data: data.daily.rejected,
                        borderColor: '#EF4444',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        fill: true,
                        tension: 0.3
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top' }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });

        // 2. SMTP Utilization Doughnut Chart
        const ctxSmtp = document.getElementById('smtpShareChart').getContext('2d');
        const colors = ['#6366F1', '#10B981', '#F59E0B', '#3B82F6', '#8B5CF6', '#EC4899'];
        new Chart(ctxSmtp, {
            type: 'doughnut',
            data: {
                labels: data.smtp.labels.length ? data.smtp.labels : ['No Data'],
                datasets: [{
                    data: data.smtp.counts.length ? data.smtp.counts : [1],
                    backgroundColor: colors.slice(0, data.smtp.labels.length || 1)
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });

        // 3. Campaign Comparison Bar Chart
        const ctxCamp = document.getElementById('campaignCompareChart').getContext('2d');
        const campTitles = data.campaigns.map(c => c.title);
        const campSent = data.campaigns.map(c => c.sent_count);
        const campFailed = data.campaigns.map(c => c.failed_count);

        new Chart(ctxCamp, {
            type: 'bar',
            data: {
                labels: campTitles.length ? campTitles : ['None'],
                datasets: [
                    {
                        label: 'Sent',
                        data: campSent,
                        backgroundColor: '#6366F1'
                    },
                    {
                        label: 'Failed',
                        data: campFailed,
                        backgroundColor: '#EF4444'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    });
});
</script>
