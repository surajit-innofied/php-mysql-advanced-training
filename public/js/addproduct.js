



        function updateCategoryDropdown() {
            const typeSelect = document.getElementById('category_type');
            const categorySelect = document.getElementById('category');
            const type = typeSelect.value.toLowerCase();

            categorySelect.innerHTML = '<option value="">-- Select Category --</option>';
            if (categoriesByType[type]) {
                categoriesByType[type].forEach(cat => {
                    const opt = document.createElement('option');
                    opt.value = cat.id;
                    opt.textContent = cat.name;
                    categorySelect.appendChild(opt);
                });
            }
        }

        document.getElementById('category_type').addEventListener('change', function() {
            const weightField = document.getElementById('weightField');
            const fileField = document.getElementById('fileField');

            if (this.value === 'physical') {
                weightField.style.display = 'block';
                fileField.style.display = 'none';
            } else if (this.value === 'digital') {
                fileField.style.display = 'block';
                weightField.style.display = 'none';
            } else {
                weightField.style.display = 'none';
                fileField.style.display = 'none';
            }
        });
   