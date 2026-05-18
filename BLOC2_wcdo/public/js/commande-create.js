// Sprint 12 — UX page création commande
// Vanilla JS, pas de framework. Voir app/Views/commandes/create.php
(function () {
    'use strict';

    var form = document.querySelector('.order-form');
    if (!form) return;

    var instanceCounter = 0;

    function formatPrice(n) {
        return n.toFixed(2).replace('.', ',') + '\u00a0€';
    }

    // ------------------------------------------------------------------
    // Compteurs [−] [N] [+] : met à jour le hidden input + dispatch change
    // ------------------------------------------------------------------
    function initCounters() {
        form.addEventListener('click', function (e) {
            var btn = e.target.closest('.counter-btn');
            if (!btn) return;
            e.preventDefault();
            var counter = btn.closest('.counter');
            var valueEl = counter.querySelector('.counter-value');
            var hidden  = counter.parentElement.querySelector('input[type="hidden"]');
            var current = parseInt(valueEl.textContent, 10) || 0;
            var delta   = btn.classList.contains('plus') ? 1 : -1;
            var next    = Math.max(0, Math.min(99, current + delta));
            if (next === current) return;
            valueEl.textContent = String(next);
            if (hidden) {
                hidden.value = String(next);
                hidden.dispatchEvent(new Event('change', { bubbles: true }));
            }
        });
    }

    // ------------------------------------------------------------------
    // Toggle type de service : marque la carte sélectionnée
    // ------------------------------------------------------------------
    function initServiceToggle() {
        var cards = form.querySelectorAll('.service-card');
        function refresh() {
            cards.forEach(function (card) {
                var input = card.querySelector('input[type="radio"]');
                card.classList.toggle('is-selected', input && input.checked);
            });
        }
        cards.forEach(function (card) {
            card.querySelector('input[type="radio"]').addEventListener('change', refresh);
        });
        refresh();
    }

    // ------------------------------------------------------------------
    // Onglets Produits / Menus
    // ------------------------------------------------------------------
    function initTabs() {
        var tabs   = form.querySelectorAll('.tab');
        var panels = {
            produits: document.getElementById('tab-produits'),
            menus:    document.getElementById('tab-menus'),
        };
        tabs.forEach(function (tab) {
            tab.addEventListener('click', function (e) {
                e.preventDefault();
                var target = tab.dataset.tab;
                tabs.forEach(function (t) { t.classList.toggle('active', t === tab); });
                Object.keys(panels).forEach(function (k) {
                    if (panels[k]) panels[k].classList.toggle('hidden', k !== target);
                });
            });
        });
    }

    // ------------------------------------------------------------------
    // Menus : validation, sérialisation, instances
    // ------------------------------------------------------------------
    function validateMenuSections(menuBlock) {
        var valid = true;
        menuBlock.querySelectorAll('.section-block').forEach(function (section) {
            var min    = parseInt(section.dataset.min || '0', 10);
            var picked = section.querySelectorAll('input:checked').length;
            var invalid = picked < min;
            section.classList.toggle('is-invalid', invalid);
            if (invalid) valid = false;
        });
        return valid;
    }

    function serializeMenuChoices(menuBlock) {
        var choix      = [];
        var suppTotal  = 0;
        var labelParts = [];
        menuBlock.querySelectorAll('.section-block').forEach(function (section) {
            var idSection = section.dataset.sectionId;
            var max       = parseInt(section.dataset.max || '1', 10);
            section.querySelectorAll('input:checked').forEach(function (input) {
                var supp  = parseFloat(input.dataset.supplement) || 0;
                var label = (input.dataset.label || '').trim();
                suppTotal += supp;
                if (label) labelParts.push(label + (supp > 0 ? ' (+' + formatPrice(supp) + ')' : ''));
                choix.push({ idSection: idSection, idProduit: input.value, multiple: max > 1 });
            });
        });
        return { labelParts: labelParts, supplement: suppTotal, choix: choix };
    }

    function addMenuInstance(menuBlock) {
        if (!validateMenuSections(menuBlock)) return false;
        var idMenu    = parseInt(menuBlock.dataset.id, 10);
        var prixMenu  = parseFloat(menuBlock.dataset.prix) || 0;
        var menuNom   = menuBlock.querySelector('.menu-title h3').textContent.trim();
        var instances = menuBlock.querySelector('.menu-instances');
        var info      = serializeMenuChoices(menuBlock);
        var idx       = instanceCounter++;

        var row = document.createElement('div');
        row.className = 'menu-instance';
        row.dataset.prix            = prixMenu.toFixed(2);
        row.dataset.supplementTotal = info.supplement.toFixed(2);
        row.dataset.label           = menuNom + (info.labelParts.length ? ' — ' + info.labelParts.join(', ') : '');

        var frag = document.createElement('div');
        frag.className = 'menu-instance-hidden';
        var hId = document.createElement('input');
        hId.type = 'hidden';
        hId.name = 'menu_instances[' + idx + '][id_menu]';
        hId.value = String(idMenu);
        frag.appendChild(hId);
        info.choix.forEach(function (c) {
            var h = document.createElement('input');
            h.type  = 'hidden';
            h.name  = 'menu_instances[' + idx + '][sections][' + c.idSection + ']' + (c.multiple ? '[]' : '');
            h.value = c.idProduit;
            frag.appendChild(h);
        });
        row.appendChild(frag);

        var labelEl = document.createElement('span');
        labelEl.className   = 'menu-instance-label';
        labelEl.textContent = row.dataset.label + ' — ' + formatPrice(prixMenu + info.supplement);
        row.appendChild(labelEl);

        var removeBtn = document.createElement('button');
        removeBtn.type      = 'button';
        removeBtn.className = 'menu-instance-remove';
        removeBtn.setAttribute('aria-label', 'Supprimer');
        removeBtn.textContent = '\u00d7';
        removeBtn.addEventListener('click', function () { row.remove(); recomputeCart(); });
        row.appendChild(removeBtn);

        instances.appendChild(row);

        menuBlock.querySelectorAll('.section-block input').forEach(function (i) {
            i.checked = false;
            i.closest('.section-block').classList.remove('is-invalid');
        });
        return true;
    }

    function initMenus() {
        form.querySelectorAll('.menu-block').forEach(function (block) {
            var header   = block.querySelector('.menu-header');
            var sections = block.querySelector('.menu-sections');
            var addBtn   = block.querySelector('.btn-add-menu');
            if (header && sections) {
                header.addEventListener('click', function () {
                    var open = !block.classList.contains('is-open');
                    block.classList.toggle('is-open', open);
                    sections.hidden = !open;
                });
            }
            if (addBtn) {
                addBtn.addEventListener('click', function (e) {
                    e.stopPropagation();
                    if (addMenuInstance(block)) {
                        block.classList.remove('is-open');
                        if (sections) sections.hidden = true;
                        recomputeCart();
                    }
                });
            }
        });
    }

    // ------------------------------------------------------------------
    // Contraintes sections : limite checkboxes à data-max
    // ------------------------------------------------------------------
    function initSectionConstraints() {
        form.querySelectorAll('.section-block').forEach(function (section) {
            var max = parseInt(section.dataset.max || '1', 10);
            section.addEventListener('change', function (e) {
                if (e.target.type !== 'checkbox') return;
                var checked = section.querySelectorAll('input[type="checkbox"]:checked');
                if (checked.length > max) {
                    e.target.checked = false;
                }
            });
        });
    }

    // ------------------------------------------------------------------
    // Calcule le panier et l'affiche dans la sidebar
    // ------------------------------------------------------------------
    function recomputeCart() {
        var items = [];
        var total = 0;

        // Produits
        form.querySelectorAll('.product-card').forEach(function (card) {
            var hidden = card.querySelector('input[type="hidden"]');
            var qte    = parseInt(hidden.value, 10) || 0;
            var prix   = parseFloat(card.dataset.prix) || 0;
            card.classList.toggle('is-active', qte > 0);
            if (qte > 0) {
                var sous = qte * prix;
                total += sous;
                items.push({
                    label: qte + ' × ' + card.querySelector('.product-name').textContent.trim(),
                    sous:  sous,
                });
            }
        });

        // Menus (instances validées et ajoutées)
        form.querySelectorAll('.menu-instance').forEach(function (inst) {
            var prix = parseFloat(inst.dataset.prix) || 0;
            var supp = parseFloat(inst.dataset.supplementTotal) || 0;
            var sous = prix + supp;
            total += sous;
            items.push({ label: inst.dataset.label, sous: sous });
        });

        // Affichage
        var list = form.querySelector('.cart-items');
        list.innerHTML = '';
        if (items.length === 0) {
            var empty = document.createElement('li');
            empty.className  = 'cart-empty';
            empty.textContent = 'Aucun article sélectionné.';
            list.appendChild(empty);
        } else {
            items.forEach(function (it) {
                var li = document.createElement('li');
                li.className = 'cart-item';
                var span = document.createElement('span');
                span.textContent = it.label;
                var strong = document.createElement('strong');
                strong.textContent = formatPrice(it.sous);
                li.appendChild(span);
                li.appendChild(strong);
                list.appendChild(li);
            });
        }

        form.querySelector('.cart-total-value').textContent = formatPrice(total);
        form.querySelector('.cart-count-value').textContent = String(items.length);

        var canSubmit = items.length > 0;
        var cta = form.querySelector('.btn-cta');
        cta.disabled = !canSubmit;
        form.querySelector('.cart-error').classList.add('hidden');
    }

    function init() {
        initCounters();
        initServiceToggle();
        initTabs();
        initMenus();
        initSectionConstraints();
        form.addEventListener('change', recomputeCart);
        recomputeCart();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
