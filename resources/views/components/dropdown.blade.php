@props(['align' => 'right', 'width' => '48', 'contentClasses' => 'py-1 bg-white'])

@php
$alignmentClasses = match ($align) {
    'left' => 'ltr:origin-top-left rtl:origin-top-right start-0',
    'top' => 'origin-top',
    default => 'ltr:origin-top-right rtl:origin-top-left end-0',
};

$width = match ($width) {
    '48' => 'w-48',
    default => $width,
};
@endphp

<div class="relative">
    <div onclick="toggleDropdown(this)">
        {{ $trigger }}
    </div>

    <div 
        class="absolute z-50 mt-2 {{ $width }} rounded-md shadow-lg {{ $alignmentClasses }} hidden"
    >
        <div class="rounded-md ring-1 ring-black ring-opacity-5 {{ $contentClasses }}">
            {{ $content }}
        </div>
    </div>
</div>

<script>
    function toggleDropdown(trigger) {
        const dropdown = trigger.nextElementSibling;
        const isVisible = dropdown.style.display === 'block';

        // Hide all dropdowns
        document.querySelectorAll('[onclick="toggleDropdown(this)"] + div').forEach(el => {
            el.style.display = 'none';
        });

        // Toggle current dropdown
        dropdown.style.display = isVisible ? 'none' : 'block';
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        const dropdowns = document.querySelectorAll('[onclick="toggleDropdown(this)"] + div');
        dropdowns.forEach(dropdown => {
            if (!dropdown.contains(event.target) && !dropdown.previousElementSibling.contains(event.target)) {
                dropdown.style.display = 'none';
            }
        });
    });
</script>
