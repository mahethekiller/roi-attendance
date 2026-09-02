<x-admin-layout>
    <!-- Page Header & Action Buttons -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div>
            <h2 class="fw-bold text-body-emphasis mb-1">API Access Tokens</h2>
            <p class="text-body-secondary mb-0">Generate, monitor, and revoke personal access Bearer tokens for external REST API clients.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.api-docs.index') }}" class="btn btn-outline-secondary btn-sm px-3 shadow-sm d-flex align-items-center gap-2">
                <i data-lucide="book-open" style="width: 16px; height: 16px;"></i>
                <span>View API Documentation</span>
            </a>
            <button type="button" class="btn btn-primary btn-sm px-3 shadow-sm d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#createTokenModal">
                <i data-lucide="plus-circle" style="width: 16px; height: 16px;"></i>
                <span>Generate New Token</span>
            </button>
        </div>
    </div>

    <!-- Feedback Alerts -->
    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4 d-flex align-items-center gap-2" role="alert">
            <i data-lucide="check-circle" style="width: 20px; height: 20px;"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm mb-4 d-flex align-items-center gap-2" role="alert">
            <i data-lucide="alert-circle" style="width: 20px; height: 20px;"></i>
            <div>{{ session('error') }}</div>
        </div>
    @endif

    <!-- Plaintext Token Reveal Banner -->
    @if(session('newToken'))
        <div class="card border-primary mb-4 shadow-sm" style="border-width: 2px;">
            <div class="card-header bg-primary bg-opacity-10 border-primary d-flex align-items-center gap-2 text-primary fw-bold">
                <i data-lucide="key" style="width: 20px; height: 20px;"></i>
                <span>Your New API Token for '{{ session('tokenName') }}'</span>
            </div>
            <div class="card-body">
                <div class="alert alert-warning border-0 d-flex align-items-center gap-2 mb-3">
                    <i data-lucide="alert-triangle" style="width: 18px; height: 18px;"></i>
                    <small>Please copy this token now. For security purposes, it will never be displayed again.</small>
                </div>
                <div class="input-group">
                    <input type="text" id="plainTokenInput" class="form-control font-monospace bg-body-tertiary text-body fw-bold" value="{{ session('newToken') }}" readonly>
                    <button class="btn btn-primary d-flex align-items-center gap-1" onclick="copyToken()">
                        <i data-lucide="copy" style="width: 16px; height: 16px;"></i>
                        <span id="copyBtnText">Copy Token</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Active Tokens Card -->
    <div class="card border-0 shadow-sm bg-body text-body">
        <div class="card-header bg-body border-bottom p-3 d-flex align-items-center justify-content-between">
            <h6 class="fw-bold mb-0 text-body-emphasis">Active API Tokens ({{ $tokens->count() }})</h6>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-body-secondary">
                    <tr>
                        <th>Token Name</th>
                        <th>Abilities</th>
                        <th>Last Used</th>
                        <th>Created At</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tokens as $token)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-2">
                                        <i data-lucide="key" style="width: 16px; height: 16px;"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold text-body-emphasis">{{ $token->name }}</div>
                                        <span class="text-body-secondary small">ID: #{{ $token->id }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @foreach($token->abilities as $ability)
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle fw-mono">{{ $ability }}</span>
                                @endforeach
                            </td>
                            <td>
                                @if($token->last_used_at)
                                    <span class="text-body-emphasis small">{{ $token->last_used_at->diffForHumans() }}</span>
                                @else
                                    <span class="badge bg-body-tertiary text-body-secondary border">Never used</span>
                                @endif
                            </td>
                            <td class="text-body-secondary small">{{ $token->created_at->format('M d, Y h:i A') }}</td>
                            <td class="text-end">
                                <form method="POST" action="{{ route('admin.api-tokens.destroy', $token->id) }}" class="d-inline" onsubmit="return confirm('Revoke token \'{{ $token->name }}\'? External clients using it will immediately lose access.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger d-inline-flex align-items-center gap-1">
                                        <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
                                        <span>Revoke</span>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-body-secondary">
                                <i data-lucide="key" class="mb-2 d-block mx-auto text-body-tertiary" style="width: 36px; height: 36px;"></i>
                                <span>No API access tokens created yet. Click "Generate New Token" above.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Create Token Modal -->
    <div class="modal fade" id="createTokenModal" tabindex="-1" aria-labelledby="createTokenModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-body border-0 shadow">
                <form method="POST" action="{{ route('admin.api-tokens.store') }}">
                    @csrf
                    <div class="modal-header border-bottom">
                        <h5 class="modal-title fw-bold text-body-emphasis" id="createTokenModalLabel">
                            <i data-lucide="plus-circle" class="text-primary me-2" style="width: 20px; height: 20px;"></i>
                            Generate API Access Token
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-body">
                        <div class="mb-3">
                            <label for="token_name" class="form-label fw-semibold text-body-emphasis">Token Name / Client Identifier <span class="text-danger">*</span></label>
                            <input type="text" name="token_name" id="token_name" class="form-control bg-body-tertiary text-body @error('token_name') is-invalid @enderror" placeholder="e.g. HR Payroll App, Mobile Scanner, External BI" required>
                            <div class="form-text text-body-secondary">Give this token a descriptive name so you remember where it is being used.</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-body-emphasis">Token Scopes / Abilities</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="abilities[]" value="attendance:read" id="scopeAttendanceRead" checked>
                                <label class="form-check-label text-body-emphasis" for="scopeAttendanceRead">
                                    <strong>attendance:read</strong> &mdash; Read attendance punch logs & summaries
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary d-flex align-items-center gap-2">
                            <i data-lucide="key" style="width: 16px; height: 16px;"></i>
                            <span>Generate Token</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function copyToken() {
            const input = document.getElementById('plainTokenInput');
            if (input) {
                input.select();
                navigator.clipboard.writeText(input.value);
                const btnText = document.getElementById('copyBtnText');
                if (btnText) {
                    btnText.textContent = 'Copied!';
                    setTimeout(() => { btnText.textContent = 'Copy Token'; }, 2500);
                }
            }
        }
    </script>
</x-admin-layout>
