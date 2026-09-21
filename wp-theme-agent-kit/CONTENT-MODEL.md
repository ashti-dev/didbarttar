# CONTENT MODEL

This document defines content before WordPress templates are implemented.

For every entity define:
- name
- purpose
- fields
- required/optional status
- source of truth
- admin editable?
- user generated?
- relationship to other entities
- frontend consumers
- WooCommerce relationship

Typical entities:
- Page
- Post
- Product
- Product Category
- Service
- Portfolio
- Testimonial
- FAQ
- Author
- Site Settings
- Navigation

Do not create a CPT or custom field merely because it is technically possible. Each model must have a content/business reason.
