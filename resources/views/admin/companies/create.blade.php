<x-admin-layout>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-base-content tracking-tight">Add New Company</h1>
            <p class="text-sm text-base-content/70 mt-0.5">Register a new company entity for attendance tracking and employee segmentation.</p>
        </div>
        <div>
            <a href="{{ route('admin.companies.index') }}" class="btn btn-outline btn-sm gap-2">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                <span>Back to Companies</span>
            </a>
        </div>
    </div>

    <div class="flex justify-center">
        <div class="w-full max-w-2xl">
            <div class="card bg-base-100 border border-base-200/60 shadow-xs">
                <div class="card-body p-6 sm:p-8">
                    <form method="POST" action="{{ route('admin.companies.store') }}" class="space-y-5">
                        @csrf

                        <!-- Company Name -->
                        <fieldset class="fieldset">
                            <legend class="fieldset-legend text-xs font-bold uppercase tracking-wider text-base-content/70">Company Name <span class="text-error">*</span></legend>
                            <label class="input input-bordered flex items-center gap-2 w-full @error('name') input-error @enderror">
                                <i data-lucide="building" class="w-4 h-4 text-base-content/50 shrink-0"></i>
                                <input type="text" name="name" id="name" class="grow bg-transparent" placeholder="e.g. ROI Mantra Private Limited" value="{{ old('name') }}" required autofocus>
                            </label>
                            @error('name')
                                <p class="fieldset-label text-error">{{ $message }}</p>
                            @enderror
                        </fieldset>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <!-- Company Code -->
                            <fieldset class="fieldset">
                                <legend class="fieldset-legend text-xs font-bold uppercase tracking-wider text-base-content/70">Company Code (Optional)</legend>
                                <label class="input input-bordered flex items-center gap-2 w-full @error('code') input-error @enderror">
                                    <i data-lucide="hash" class="w-4 h-4 text-base-content/50 shrink-0"></i>
                                    <input type="text" name="code" id="code" class="grow bg-transparent font-mono uppercase" placeholder="e.g. ROIM, I2K2" value="{{ old('code') }}">
                                </label>
                                @error('code')
                                    <p class="fieldset-label text-error">{{ $message }}</p>
                                @enderror
                            </fieldset>

                            <!-- Status -->
                            <fieldset class="fieldset">
                                <legend class="fieldset-legend text-xs font-bold uppercase tracking-wider text-base-content/70">Status <span class="text-error">*</span></legend>
                                <select name="status" id="status" class="select select-bordered w-full @error('status') select-error @enderror" required>
                                    <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @error('status')
                                    <p class="fieldset-label text-error">{{ $message }}</p>
                                @enderror
                            </fieldset>
                        </div>

                        <!-- Description -->
                        <fieldset class="fieldset">
                            <legend class="fieldset-legend text-xs font-bold uppercase tracking-wider text-base-content/70">Description / Notes (Optional)</legend>
                            <textarea name="description" id="description" rows="3" class="textarea textarea-bordered w-full @error('description') textarea-error @enderror" placeholder="Optional notes, branch details, or legal registration info...">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="fieldset-label text-error">{{ $message }}</p>
                            @enderror
                        </fieldset>

                        <!-- Form Action Buttons -->
                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-base-200/60">
                            <a href="{{ route('admin.companies.index') }}" class="btn btn-ghost">Cancel</a>
                            <button type="submit" class="btn btn-primary px-6 gap-2">
                                <i data-lucide="save" class="w-4 h-4"></i>
                                <span>Create Company</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
