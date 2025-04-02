@extends('layouts.admin')

@section('title', 'Create Category')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Create New Category</h1>
        <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Categories
        </a>
    </div>
    
    <div class="card shadow">
        <div class="card-body">
            <form action="{{ route('admin.categories.store') }}" method="POST">
                @csrf
                
                <div class="mb-3">
                    <label for="name" class="form-label">Category Name *</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="display_order" class="form-label">Display Order</label>
                        <input type="number" class="form-control @error('display_order') is-invalid @enderror" id="display_order" name="display_order" value="{{ old('display_order', 0) }}" min="0">
                        @error('display_order')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Lower numbers will display first</div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <div class="form-check form-switch mt-4">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', 1) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Active</label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="day_specific" name="day_specific" value="1" {{ old('day_specific') ? 'checked' : '' }}>
                        <label class="form-check-label" for="day_specific">Day-Specific Category</label>
                    </div>
                    <div class="form-text">Enable if this category should only appear on specific days (e.g., "Monday Specials")</div>
                </div>
                
                <div class="mb-3" id="specific_day_section" style="{{ old('day_specific') ? '' : 'display: none;' }}">
                    <label for="specific_day" class="form-label">Specific Day</label>
                    <select class="form-select @error('specific_day') is-invalid @enderror" id="specific_day" name="specific_day">
                        <option value="">Select Day</option>
                        <option value="monday" {{ old('specific_day') == 'monday' ? 'selected' : '' }}>Monday</option>
                        <option value="tuesday" {{ old('specific_day') == 'tuesday' ? 'selected' : '' }}>Tuesday</option>
                        <option value="wednesday" {{ old('specific_day') == 'wednesday' ? 'selected' : '' }}>Wednesday</option>
                        <option value="thursday" {{ old('specific_day') == 'thursday' ? 'selected' : '' }}>Thursday</option>
                        <option value="friday" {{ old('specific_day') == 'friday' ? 'selected' : '' }}>Friday</option>
                        <option value="saturday" {{ old('specific_day') == 'saturday' ? 'selected' : '' }}>Saturday</option>
                        <option value="sunday" {{ old('specific_day') == 'sunday' ? 'selected' : '' }}>Sunday</option>
                    </select>
                    @error('specific_day')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Save Category
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const daySpecificCheckbox = document.getElementById('day_specific');
    const specificDaySection = document.getElementById('specific_day_section');
    
    daySpecificCheckbox.addEventListener('change', function() {
        specificDaySection.style.display = this.checked ? 'block' : 'none';
    });
});
</script>
@endpush

@endsection 