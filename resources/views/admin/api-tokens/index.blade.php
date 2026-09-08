<x-admin-layout>
    <!-- Page Header & Action Buttons -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-base-content tracking-tight">API Access Tokens</h1>
            <p class="text-sm text-base-content/70 mt-0.5">Generate, monitor, and revoke personal access Bearer tokens for external REST API clients.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.api-docs.index') }}" class="btn btn-outline btn-sm sm:btn-md gap-2 shadow-xs">
                <i data-lucide="book-open" class="w-4 h-4"></i>
                <span>View API Documentation</span>
            </a>
            <x-authorized permission="api.tokens.manage">
                <button type="button" class="btn btn-primary btn-sm sm:btn-md gap-2 shadow-xs" onclick="document.getElementById('createTokenModal').showModal()">
                    <i data-lucide="plus-circle" class="w-4 h-4"></i>
                    <span>Generate New Token</span>
                </button>
            </x-authorized>
        </div>
    </div>

    <!-- Feedback Alerts -->
    @if(session('success'))
        <div class="alert alert-success shadow-xs mb-6" role="alert">
            <i data-lucide="check-circle" class="w-5 h-5 shrink-0"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-error shadow-xs mb-6" role="alert">
            <i data-lucide="alert-circle" class="w-5 h-5 shrink-0"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Plaintext Token Reveal Banner -->
    @if(session('newToken'))
        <div class="card bg-base-100 border-2 border-primary shadow-md mb-6 overflow-hidden">
            <div class="p-4 bg-primary/10 border-b border-primary/20 flex items-center gap-2 text-primary font-bold">
                <i data-lucide="key" class="w-5 h-5"></i>
                <span>Your New API Token for '{{ session('tokenName') }}'</span>
            </div>
            <div class="card-body p-5">
                <div class="alert alert-warning shadow-xs mb-4">
                    <i data-lucide="alert-triangle" class="w-5 h-5 shrink-0"></i>
                    <span class="text-xs sm:text-sm">Please copy this token now. For security purposes, it will never be displayed again.</span>
                </div>
                <div class="join w-full">
                    <input type="text" id="plainTokenInput" class="input input-bordered join-item w-full font-mono text-sm font-bold bg-base-200/50 text-base-content" value="{{ session('newToken') }}" readonly>
                    <button class="btn btn-primary join-item gap-2 shrink-0" onclick="copyToken()">
                        <i data-lucide="copy" class="w-4 h-4"></i>
                        <span id="copyBtnText">Copy Token</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Active Tokens Card -->
    <div class="card bg-base-100 border border-base-200/60 shadow-xs overflow-hidden">
        <div class="p-4 border-b border-base-200/60 flex items-center justify-between">
            <h2 class="font-semibold text-base-content">Active API Tokens ({{ $tokens->count() }})</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="table table-zebra w-full">
                <thead>
                    <tr class="bg-base-200/50 text-base-content/70">
                        <th>Token Name</th>
                        <th>Abilities</th>
                        <th>Last Used</th>
                        <th>Created At</th>
                        <x-authorized permission="api.tokens.manage">
                            <th class="text-right">Actions</th>
                        </x-authorized>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tokens as $token)
                        <tr class="hover:bg-base-200/40 transition-colors">
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="p-2 rounded-lg bg-primary/10 text-primary">
                                        <i data-lucide="key" class="w-4 h-4"></i>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-base-content">{{ $token->name }}</div>
                                        <span class="text-xs text-base-content/60 font-mono">ID: #{{ $token->id }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="flex flex-wrap gap-1">
                                    @foreach($token->abilities as $ability)
                                        <span class="badge badge-neutral badge-soft font-mono text-xs">{{ $ability }}</span>
                                    @endforeach
                                </div>
                            </td>
                            <td>
                                @if($token->last_used_at)
                                    <span class="text-xs text-base-content/80">{{ $token->last_used_at->diffForHumans() }}</span>
                                @else
                                    <span class="badge badge-ghost badge-soft text-base-content/50 text-xs">Never used</span>
                                @endif
                            </td>
                            <td class="text-xs text-base-content/70">{{ $token->created_at->format('M d, Y h:i A') }}</td>
                            <x-authorized permission="api.tokens.manage">
                                <td class="text-right">
                                    <form method="POST" action="{{ route('admin.api-tokens.destroy', $token->id) }}" class="inline" onsubmit="return confirm('Revoke token \'{{ $token->name }}\'? External clients using it will immediately lose access.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-ghost btn-square text-error hover:bg-error/10" aria-label="Revoke API token {{ $token->name }}" title="Revoke Token">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </td>
                            </x-authorized>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-10 text-base-content/60">
                                <i data-lucide="key" class="mb-2 mx-auto text-base-content/30 w-9 h-9"></i>
                                <span>No API access tokens created yet. Click "Generate New Token" above.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Create Token Modal -->
    <x-authorized permission="api.tokens.manage">
        <dialog id="createTokenModal" class="modal">
            <div class="modal-box bg-base-100 max-w-md border border-base-200">
                <form method="dialog">
                    <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                </form>
                <form method="POST" action="{{ route('admin.api-tokens.store') }}">
                    @csrf
                    <h3 class="font-bold text-lg text-base-content flex items-center gap-2 mb-4">
                        <i data-lucide="plus-circle" class="w-5 h-5 text-primary"></i>
                        Generate API Access Token
                    </h3>
                    <fieldset class="fieldset mb-4">
                        <legend class="fieldset-legend text-xs font-bold uppercase tracking-wider text-base-content/70">Token Name / Client Identifier <span class="text-error">*</span></legend>
                        <input type="text" name="token_name" id="token_name" class="input input-bordered w-full text-sm @error('token_name') input-error @enderror" placeholder="e.g. HR Payroll App, Mobile Scanner, External BI" required>
                        <p class="fieldset-label text-xs text-base-content/60">Give this token a descriptive name so you remember where it is being used.</p>
                    </fieldset>
                    <div class="mb-6">
                        <span class="text-xs font-bold uppercase tracking-wider text-base-content/70 block mb-2">Token Scopes / Abilities</span>
                        <label class="flex items-center gap-3 p-3 rounded-lg border border-base-200 bg-base-200/40 cursor-pointer">
                            <input type="checkbox" name="abilities[]" value="attendance:read" id="scopeAttendanceRead" class="checkbox checkbox-primary checkbox-sm" checked>
                            <span class="text-sm text-base-content">
                                <strong class="font-mono">attendance:read</strong> &mdash; Read attendance punch logs & summaries
                            </span>
                        </label>
                    </div>
                    <div class="modal-action">
                        <button type="button" class="btn btn-ghost" onclick="document.getElementById('createTokenModal').close()">Cancel</button>
                        <button type="submit" class="btn btn-primary gap-2">
                            <i data-lucide="key" class="w-4 h-4"></i>
                            <span>Generate Token</span>
                        </button>
                    </div>
                </form>
            </div>
            <form method="dialog" class="modal-backdrop">
                <button>close</button>
            </form>
        </dialog>
    </x-authorized>

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
