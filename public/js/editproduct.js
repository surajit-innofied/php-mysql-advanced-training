document.addEventListener('DOMContentLoaded', () => {
    const typeSelect = document.getElementById('category_type');
    const categorySelect = document.getElementById('category');
    const weightField = document.getElementById('weightField');
    const fileField = document.getElementById('fileField');

    // Function to handle both category dropdown and field visibility
    function handleCategoryTypeChange() {
        console.log("Category type changed. Toggling fields and updating categories.");
        const type = typeSelect.value.toLowerCase();
        
        // Toggle the visibility of the weight and file fields
        if (type === 'physical') {
            weightField.style.display = 'block';
            fileField.style.display = 'none';
        } else if (type === 'digital') {
            fileField.style.display = 'block';
            weightField.style.display = 'none';
        } else {
            weightField.style.display = 'none';
            fileField.style.display = 'none';
        }

        // Populate the category dropdown
        categorySelect.innerHTML = '<option value="">-- Select Category --</option>';
        if (categoriesByType && categoriesByType[type]) {
            categoriesByType[type].forEach(cat => {
                const opt = document.createElement('option');
                opt.value = cat.id;
                opt.textContent = cat.name;
                // Pre-select the current category if it matches
                if (cat.id === currentCategory) {
                    opt.selected = true;
                }
                categorySelect.appendChild(opt);
            });
        }
    }

    // Set initial values and trigger the handler once on load
    if (currentType) {
        typeSelect.value = currentType;
    }
    handleCategoryTypeChange();
    
    // Add event listener for category type change
    typeSelect.addEventListener('change', handleCategoryTypeChange);
});
