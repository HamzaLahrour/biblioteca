@props(['name', 'label', 'options', 'selected' => null])

<div class="mb-4">
    <label for="{{ $name }}" class="form-label" style="font-weight: 600; color: var(--text-main);">
        {{ $label }} <span class="text-danger">*</span>
    </label>
    
    <select name="{{ $name }}" 
            id="{{ $name }}" 
            class="form-select form-select-lg @error($name) is-invalid @enderror" 
            style="font-size: 1rem; border-color: #e0e0e0;">
        
        <option value="">-- Selecciona una opción --</option>
        
        @foreach($options as $option)
            <option value="{{ $option->id }}" @selected(old($name, $selected) == $option->id)>
                {{ $option->nombre }}
            </option>
        @endforeach
        
    </select>

    @error($name)
        <div class="invalid-feedback fw-medium">{{ $message }}</div>
    @enderror
</div>