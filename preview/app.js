(() => {
  'use strict';
  const recipes = Array.isArray(window.CA_RECIPES) ? window.CA_RECIPES : [];
  const categories = Array.isArray(window.CA_CATEGORIES) ? window.CA_CATEGORIES : [];
  const escapeUrl = (value) => String(value || '').replace(/[<>"'`\\]/g, '');
  const $ = (selector, root = document) => root.querySelector(selector);
  const make = (tag, className, text) => {
    const node = document.createElement(tag);
    if (className) node.className = className;
    if (text !== undefined && text !== null) node.textContent = String(text);
    return node;
  };
  const findRecipe = (slug) => recipes.find((recipe) => recipe.slug === slug);
  const toMinutes = (minutes) => {
    if (!minutes) return '';
    if (minutes < 60) return `${minutes} min`;
    const hours = Math.floor(minutes / 60);
    const remainder = minutes % 60;
    return `${hours} hr${hours > 1 ? 's' : ''}${remainder ? ` ${remainder} min` : ''}`;
  };
  const categoryIcon = {
    Chicken: '✳', Beef: '◒', Pork: '◌', Seafood: '≈', Vegetables: '❋', Potatoes: '✦',
    Breakfast: '☼', Lunch: '◍', Dinner: '✺', Appetizers: '◇', Snacks: '✧', Desserts: '❀',
    'Frozen Foods': '❄', 'Healthy Recipes': '⌁', 'Quick & Easy': '↗', Vegetarian: '❖'
  };
  const categoryTile = (name, index) => {
    const link = make('a', `category-card category-card--${(index % 4) + 1}`);
    link.href = `category.html?category=${encodeURIComponent(name)}`;
    const icon = make('span', 'category-card__icon', categoryIcon[name] || '✦');
    icon.setAttribute('aria-hidden', 'true');
    const title = make('span', 'category-card__title', name);
    const arrow = make('span', 'category-card__arrow', '↗');
    arrow.setAttribute('aria-hidden', 'true');
    link.append(icon, title, arrow);
    return link;
  };
  const recipeCard = (recipe, variant = '') => {
    const article = make('article', `recipe-tile${variant ? ` recipe-tile--${variant}` : ''}`);
    const imageLink = make('a', 'recipe-tile__image');
    imageLink.href = `recipe.html?recipe=${encodeURIComponent(recipe.slug)}`;
    imageLink.setAttribute('aria-label', `Read ${recipe.title}`);
    const image = make('img', 'ca-image');
    image.src = escapeUrl(recipe.image);
    image.alt = recipe.imageAlt || recipe.title;
    image.width = 720;
    image.height = 540;
    image.loading = 'lazy';
    image.decoding = 'async';
    imageLink.appendChild(image);
    const arrow = make('span', 'recipe-tile__image-arrow', '↗');
    arrow.setAttribute('aria-hidden', 'true');
    imageLink.appendChild(arrow);
    const body = make('div', 'recipe-tile__body');
    const eyebrow = make('div', 'recipe-tile__eyebrow');
    const categoryLink = make('a', '', recipe.category);
    categoryLink.href = `category.html?category=${encodeURIComponent(recipe.category)}`;
    eyebrow.appendChild(categoryLink);
    const totalMinutes = Number(recipe.prepMinutes || 0) + Number(recipe.cookMinutes || 0);
    if (totalMinutes > 0) {
      const time = make('span', 'recipe-tile__time');
      const clock = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
      clock.setAttribute('viewBox', '0 0 20 20');
      clock.setAttribute('aria-hidden', 'true');
      const circle = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
      circle.setAttribute('cx', '10'); circle.setAttribute('cy', '10'); circle.setAttribute('r', '7.5');
      const hands = document.createElementNS('http://www.w3.org/2000/svg', 'path');
      hands.setAttribute('d', 'M10 5.5v4.8l3 1.8');
      clock.append(circle, hands);
      time.append(clock, document.createTextNode(toMinutes(totalMinutes)));
      eyebrow.appendChild(time);
    }
    const heading = make('h3', 'recipe-tile__title');
    const titleLink = make('a', '', recipe.title);
    titleLink.href = `recipe.html?recipe=${encodeURIComponent(recipe.slug)}`;
    heading.appendChild(titleLink);
    const summary = make('p', 'recipe-tile__description', recipe.summary);
    body.append(eyebrow, heading, summary);
    article.append(imageLink, body);
    return article;
  };
  const fillRecipeGrid = (selector, items, variant = '') => {
    const grid = $(selector);
    if (!grid) return;
    grid.replaceChildren(...items.map((recipe) => recipeCard(recipe, variant)));
  };

  const renderHome = () => {
    const categoryGrid = $('#category-grid');
    if (categoryGrid) {
      const homeCategories = ['Chicken', 'Beef', 'Pork', 'Seafood', 'Vegetables', 'Potatoes', 'Breakfast', 'Desserts'];
      categoryGrid.replaceChildren(...homeCategories.map(categoryTile));
    }
    fillRecipeGrid('#featured-grid', recipes.filter((recipe) => recipe.featured).slice(0, 3));
    fillRecipeGrid('#latest-grid', recipes.filter((recipe) => !recipe.featured).slice(0, 3));
    const popularRecipes = recipes.filter((recipe) => recipe.popular).slice(0, 3);
    fillRecipeGrid('#popular-grid', popularRecipes, 'dark');
    const popularEmpty = $('#popular-empty');
    if (popularEmpty) popularEmpty.hidden = popularRecipes.length > 0;
    const newsletter = $('#preview-newsletter-form');
    if (newsletter) {
      newsletter.addEventListener('submit', (event) => {
        event.preventDefault();
        const status = $('#newsletter-status');
        if (status) status.textContent = 'This design preview does not save or send email addresses. Connect a newsletter provider in WordPress to enable sign-ups.';
        newsletter.reset();
      });
    }
  };

  const renderArchive = () => {
    const grid = $('#archive-grid');
    if (!grid) return;
    const params = new URLSearchParams(window.location.search);
    const query = (params.get('search') || '').trim().toLocaleLowerCase();
    const initialCategory = params.get('category') || '';
    const selectedCategory = { value: initialCategory };
    const popularOnly = params.get('popular') === 'true';
    const chipBox = $('#filter-chips');
    const input = $('#archive-search');
    if (input) input.value = params.get('search') || '';

    const filterRecipes = () => {
      const words = query.split(/\s+/).filter(Boolean);
      const result = recipes.filter((recipe) => {
        if (selectedCategory.value && recipe.category.toLocaleLowerCase() !== selectedCategory.value.toLocaleLowerCase()) return false;
        if (popularOnly && !recipe.popular) return false;
        const haystack = (recipe.previewOnly
          ? [recipe.title, recipe.category]
          : [recipe.title, recipe.summary, recipe.category, recipe.course, recipe.cuisine, ...(recipe.tags || []), ...(recipe.ingredients || [])]
        ).join(' ').toLocaleLowerCase();
        return words.every((word) => haystack.includes(word));
      });
      grid.replaceChildren(...result.map((recipe) => recipeCard(recipe)));
      const count = $('#result-count');
      const empty = $('#archive-empty');
      const note = $('#result-note');
      if (count) count.textContent = `${result.length} ${result.length === 1 ? 'recipe' : 'recipes'} found`;
      if (empty) empty.hidden = result.length !== 0;
      if (note) {
        const labels = [];
        if (query) labels.push(`Search: “${params.get('search')}”`);
        if (selectedCategory.value) labels.push(selectedCategory.value);
        if (popularOnly) labels.push('Popular picks');
        note.textContent = labels.join(' · ');
      }
      const title = $('#archive-title');
      const kicker = $('#archive-kicker');
      const description = $('#archive-description');
      const breadcrumb = $('#archive-breadcrumb');
      if (title && query) title.textContent = `Search results for “${params.get('search')}”`;
      else if (title && selectedCategory.value) title.textContent = `Air Fryer ${selectedCategory.value} Recipes`;
      else if (title && popularOnly) title.textContent = 'Popular air fryer recipes';
      else if (title) title.textContent = 'All air fryer recipes';
      if (kicker) kicker.textContent = query ? 'Search the collection' : (selectedCategory.value ? 'Browse by category' : 'The recipe collection');
      if (description) description.textContent = selectedCategory.value ? `Browse practical air-fryer ${selectedCategory.value.toLowerCase()} recipes with clear steps and useful timing.` : 'Browse practical recipes for weeknight dinners, sides, snacks, and something sweet.';
      if (breadcrumb) breadcrumb.textContent = selectedCategory.value || (query ? 'Search' : 'Recipes');
      document.title = `${title ? title.textContent : 'All air fryer recipes'} — Crisp & Air`;
    };

    if (chipBox) {
      const visibleCategories = ['All', ...categories];
      chipBox.replaceChildren(...visibleCategories.map((name) => {
        const button = make('button', 'filter-chip', name);
        button.type = 'button';
        button.setAttribute('aria-pressed', String(name === 'All' ? !selectedCategory.value : name.toLocaleLowerCase() === selectedCategory.value.toLocaleLowerCase()));
        button.addEventListener('click', () => {
          selectedCategory.value = name === 'All' ? '' : name;
          const newUrl = new URL(window.location.href);
          if (selectedCategory.value) newUrl.searchParams.set('category', selectedCategory.value);
          else newUrl.searchParams.delete('category');
          history.replaceState({}, '', newUrl);
          chipBox.querySelectorAll('.filter-chip').forEach((chip) => chip.setAttribute('aria-pressed', String(chip.textContent === (selectedCategory.value || 'All'))));
          filterRecipes();
        });
        return button;
      }));
    }
    const clear = $('#clear-filter');
    if (clear) clear.addEventListener('click', () => {
      selectedCategory.value = '';
      if (input) input.value = '';
      const newUrl = new URL(window.location.href);
      newUrl.searchParams.delete('category'); newUrl.searchParams.delete('search'); newUrl.searchParams.delete('popular');
      history.replaceState({}, '', newUrl);
      if (chipBox) chipBox.querySelectorAll('.filter-chip').forEach((chip) => chip.setAttribute('aria-pressed', String(chip.textContent === 'All')));
      window.location.reload();
    });
    filterRecipes();
  };

  const addFact = (container, label, value) => {
    if (!value) return;
    const item = make('div', 'recipe-facts__item');
    item.append(make('span', '', label), make('strong', '', value));
    container.appendChild(item);
  };
  const addCardFact = (container, label, value) => {
    if (!value) return;
    const item = document.createElement('div');
    item.append(make('span', '', label), make('strong', '', value));
    container.appendChild(item);
  };
  const fillList = (selector, items, type = 'li') => {
    const container = $(selector);
    if (!container) return;
    container.replaceChildren(...items.map((item, index) => {
      const isArticleStep = type === 'li' && selector === '#article-instructions';
      const node = make(type, '', isArticleStep ? '' : (typeof item === 'string' ? item : ''));
      if (isArticleStep) {
        const number = make('span', 'instruction-list__number', String(index + 1));
        number.setAttribute('aria-hidden', 'true');
        const text = make('div', '', item);
        node.append(number, text);
      }
      return node;
    }));
  };
  const renderFaqs = (items) => {
    const container = $('#recipe-faqs');
    if (!container) return;
    container.replaceChildren(...items.map((faq) => {
      const details = document.createElement('details');
      const summary = make('summary', '', faq.question);
      const answer = make('div', 'faq-list__answer');
      answer.appendChild(make('p', '', faq.answer));
      details.append(summary, answer);
      return details;
    }));
  };
  const setSectionVisibility = (selector, show) => {
    const element = $(selector);
    if (element) element.hidden = !show;
  };

  const renderRecipe = () => {
    if (!$('#recipe-title')) return;
    const params = new URLSearchParams(window.location.search);
    const recipe = findRecipe(params.get('recipe')) || recipes[0];
    if (!recipe) return;
    document.title = `${recipe.title} — Crisp & Air`;
    const metaDescription = $('meta[name="description"]');
    if (metaDescription) metaDescription.content = recipe.summary;
    const title = $('#recipe-title');
    title.textContent = recipe.title;
    $('#recipe-summary').textContent = recipe.summary;
    $('#recipe-intro').textContent = recipe.intro;
    const image = $('#recipe-image');
    image.src = escapeUrl(recipe.image);
    image.alt = recipe.imageAlt || recipe.title;
    const cardImage = $('#card-image');
    cardImage.src = escapeUrl(recipe.image);
    cardImage.alt = recipe.imageAlt || recipe.title;
    const categoryLink = $('#recipe-category-link');
    categoryLink.textContent = recipe.category;
    categoryLink.href = `category.html?category=${encodeURIComponent(recipe.category)}`;
    const factBox = $('#recipe-facts');
    factBox.replaceChildren();
    if (recipe.previewOnly) {
      addFact(factBox, 'Prep', 'Add verified time');
      addFact(factBox, 'Cook', 'Add verified time');
      addFact(factBox, 'Serves', 'Add verified yield');
    } else {
      addFact(factBox, 'Prep', toMinutes(recipe.prepMinutes));
      addFact(factBox, 'Cook', toMinutes(recipe.cookMinutes));
      addFact(factBox, 'Total', toMinutes(recipe.prepMinutes + recipe.cookMinutes));
      addFact(factBox, 'Serves', recipe.servings);
      addFact(factBox, 'Course', recipe.course);
      addFact(factBox, 'Difficulty', recipe.difficulty);
    }
    const temperatureDetail = $('#temperature-detail');
    temperatureDetail.textContent = recipe.previewOnly
      ? 'Add a verified temperature and cook time after testing.'
      : `${recipe.temperature} · ${toMinutes(recipe.cookMinutes)}`;
    $('#recipe-card-title').textContent = recipe.title;
    $('#card-description').textContent = recipe.summary;
    $('#card-url').textContent = `${window.location.origin}${window.location.pathname}?recipe=${recipe.slug}`;
    const cardMeta = $('#card-meta');
    cardMeta.replaceChildren();
    if (recipe.previewOnly) {
      addCardFact(cardMeta, 'Prep', 'To be verified');
      addCardFact(cardMeta, 'Cook', 'To be verified');
      addCardFact(cardMeta, 'Yield', 'To be confirmed');
      addCardFact(cardMeta, 'Temperature', 'To be verified');
    } else {
      addCardFact(cardMeta, 'Prep', toMinutes(recipe.prepMinutes));
      addCardFact(cardMeta, 'Cook', toMinutes(recipe.cookMinutes));
      addCardFact(cardMeta, 'Total', toMinutes(recipe.prepMinutes + recipe.cookMinutes));
      addCardFact(cardMeta, 'Yield', recipe.servings);
      addCardFact(cardMeta, 'Temperature', recipe.temperature);
    }
    addCardFact(cardMeta, 'Course', recipe.course);
    const buildIngredients = (selector, checklist) => {
      const list = $(selector);
      list.replaceChildren(...recipe.ingredients.map((ingredient, index) => {
        const item = document.createElement('li');
        if (checklist) {
          const label = document.createElement('label');
          const input = document.createElement('input');
          input.type = 'checkbox';
          input.name = `ingredient-${recipe.slug}-${index}`;
          const text = make('span', '', ingredient);
          label.append(input, text);
          item.appendChild(label);
        } else {
          item.textContent = ingredient;
        }
        return item;
      }));
    };
    buildIngredients('#article-ingredients', false);
    buildIngredients('#card-ingredients', true);
    fillList('#article-instructions', recipe.instructions);
    fillList('#card-instructions', recipe.instructions);
    fillList('#recipe-tips', recipe.tips || []);
    fillList('#recipe-variations', recipe.variations || []);
    fillList('#recipe-substitutions', recipe.substitutions || []);
    renderFaqs(recipe.faqs || []);
    $('#recipe-storage').textContent = recipe.storage || '';
    $('#recipe-reheating').textContent = recipe.reheating || '';
    $('#card-notes').textContent = recipe.notes || '';
    setSectionVisibility('#storage-section', Boolean(recipe.storage));
    setSectionVisibility('#reheating-section', Boolean(recipe.reheating));
    setSectionVisibility('#card-notes-wrap', Boolean(recipe.notes));
    setSectionVisibility('#recipe-tips', Boolean((recipe.tips || []).length));
    setSectionVisibility('#expert-tips', Boolean((recipe.tips || []).length));
    setSectionVisibility('#variations', Boolean((recipe.variations || []).length));
    setSectionVisibility('#substitutions', Boolean((recipe.substitutions || []).length));
    setSectionVisibility('#faq', Boolean((recipe.faqs || []).length));
    const breadcrumb = $('#recipe-breadcrumbs');
    breadcrumb.replaceChildren();
    const homeItem = make('li');
    const homeLink = make('a', '', 'Home'); homeLink.href = 'index.html'; homeItem.appendChild(homeLink);
    const categoryItem = make('li');
    const catLink = make('a', '', recipe.category); catLink.href = `category.html?category=${encodeURIComponent(recipe.category)}`; categoryItem.appendChild(catLink);
    const currentItem = make('li'); const current = make('span', '', recipe.title); current.setAttribute('aria-current', 'page'); currentItem.appendChild(current);
    breadcrumb.append(homeItem, categoryItem, currentItem);
    const related = recipes.filter((item) => item.slug !== recipe.slug && item.category === recipe.category).slice(0, 3);
    const relatedItems = related.length ? related : recipes.filter((item) => item.slug !== recipe.slug).slice(0, 3);
    fillRecipeGrid('#related-grid', relatedItems);
    // This standalone preview contains placeholder copy only; never emit Recipe structured data for it.
  };

  const renderPage = () => {
    const content = $('#page-content');
    if (!content) return;
    const slug = new URLSearchParams(window.location.search).get('slug') || 'about';
    const pages = {
      about: {
        title: 'About Crisp & Air',
        deck: 'A practical home-cooking resource dedicated to air-fryer recipes.',
        sections: [
          ['A little about us', 'Crisp & Air is a focused cooking publication for people who want more from the air fryer already on their counter. We bring recipes, timing guidance, and useful kitchen notes together in one easy-to-browse place.'],
          ['Useful first, always', 'Our goal is to make a recipe easy to understand before you start cooking. Each guide is designed to keep the ingredients clear, the method practical, and doneness cues visible.'],
          ['A note on credentials', 'Crisp & Air does not claim medical, nutrition, or professional culinary credentials. When nutrition information is included, it should be treated as an estimate; follow your appliance guidance and check food safety where appropriate.'],
          ['Say hello', 'Questions, corrections, and helpful notes are welcome. Visit the Contact page to reach the site editor.']
        ]
      },
      contact: {
        title: 'Contact Crisp & Air',
        deck: 'Questions, corrections, or a note about a recipe? We would love to hear from you.',
        sections: [['Get in touch', 'Use the form below to send a note to the site editor. In the WordPress version, contact messages are routed to the email configured in Settings → General.']]
      },
      'privacy-policy': {
        title: 'Privacy Policy',
        deck: 'An editable starter notice. Review and adapt it to your actual site before launch.',
        sections: [
          ['Information you send us', 'If you use the contact form, your name, email address, and message are sent to the site’s configured WordPress email address so the editor can reply. The form does not create a public profile or store messages in the recipe database. Your email provider and web host may retain message or security logs. A short-lived, salted hash may be used to limit form abuse.'],
          ['Cookies and third-party services', 'The Crisp & Air theme does not add an advertising or analytics tracker by itself. WordPress, the hosting provider, embedded content, analytics, advertising, and newsletter providers may use cookies or process technical data when enabled. Identify each provider, purpose, retention period, and visitor choice here before activating those services.'],
          ['Your choices and contact', 'For questions or a request about information you submitted, use the Contact page. Add the site owner’s identity, applicable rights, retention practices, and any required privacy-request process before launch.']
        ]
      },
      'terms-conditions': {
        title: 'Terms & Conditions',
        deck: 'A general starter for editing to fit your actual site and jurisdiction.',
        sections: [
          ['Using this site', 'Recipe information is provided for general home-cooking use. Appliances, ingredient sizes, and results vary. Follow your appliance manufacturer’s instructions and use appropriate food-safety practices. Do not rely on this site for medical or dietary advice.'],
          ['Content and external services', 'Before launch, state who owns the site content, how permission to reuse it works, how reader-submitted material is handled, and which third-party services or advertising terms apply.'],
          ['Contact', 'Questions about these terms can be sent through the Contact page. This editable starter is not legal advice.']
        ]
      },
      'cookie-policy': {
        title: 'Cookie Policy',
        deck: 'A starting point. Add the services and cookie details that match the live site.',
        sections: [
          ['Cookies on this site', 'The theme itself does not install advertising or analytics cookies. WordPress, the host, and optional third-party tools may use cookies or similar technologies for essential functions, measurement, embedded media, advertising, or subscriptions.'],
          ['Review before enabling services', 'Add a current list of the cookies and providers used on this site, their purposes, durations, and the visitor controls available in your jurisdiction. Review this page whenever an integration changes.']
        ]
      },
      disclaimer: {
        title: 'Disclaimer',
        deck: 'Practical cooking information, not a substitute for food-safety guidance or personal medical advice.',
        sections: [
          ['Cooking and food safety', 'Air-fryer results vary by appliance, food thickness, starting temperature, and basket loading. Recipe times are guides, not a substitute for checking doneness. Follow current food-safety guidance and your appliance manual; use a food thermometer when appropriate.'],
          ['Nutrition and health', 'Any nutrition figures, when present, are estimates for the stated serving and can vary with ingredients and brands. This site is for general cooking information, not medical, nutrition, or dietary advice. Consult a qualified professional for personal health needs.'],
          ['Editorial and advertising', 'Disclose any sponsorship, affiliate relationship, or paid placement clearly and near the relevant content. Do not present advertising as an independent recommendation.']
        ]
      }
    };
    const page = pages[slug] || pages.about;
    document.title = `${page.title} — Crisp & Air`;
    $('#page-title').textContent = page.title;
    $('#page-breadcrumb').textContent = page.title;
    $('#page-deck').textContent = page.deck;
    const meta = $('meta[name="description"]');
    if (meta) meta.content = page.deck;
    content.replaceChildren(...page.sections.map(([heading, body]) => {
      const section = make('section', 'preview-page-section');
      section.append(make('h2', '', heading), make('p', '', body));
      return section;
    }));
    if (slug === 'contact') {
      const form = document.createElement('form');
      form.className = 'contact-form';
      form.setAttribute('data-preview-contact', '');
      const fields = [
        ['Your name', 'text', 'name'], ['Email address', 'email', 'email'], ['Subject', 'text', 'subject']
      ];
      fields.forEach(([labelText, type, name]) => {
        const label = make('label', '', labelText);
        const input = document.createElement('input'); input.type = type; input.name = name; input.required = true;
        label.appendChild(input); form.appendChild(label);
      });
      const messageLabel = make('label', '', 'Message');
      const textarea = document.createElement('textarea'); textarea.name = 'message'; textarea.rows = 6; textarea.required = true;
      messageLabel.appendChild(textarea); form.appendChild(messageLabel);
      const note = make('p', 'contact-form__privacy', 'Preview only: this form does not send or store a message. The WordPress contact form sends messages to the site email configured in Settings → General.');
      const button = make('button', 'button button--dark', 'Send message →'); button.type = 'submit';
      const status = make('p', 'contact-form__notice'); status.hidden = true; status.setAttribute('role', 'status');
      form.append(note, button, status);
      form.addEventListener('submit', (event) => {
        event.preventDefault(); status.hidden = false; status.textContent = 'Thanks for trying the preview. No message was sent or saved.'; form.reset();
      });
      content.appendChild(form);
    }
  };

  renderHome();
  renderArchive();
  renderRecipe();
  renderPage();
})();
