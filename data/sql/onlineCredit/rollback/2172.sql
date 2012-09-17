delete from wp_postmeta where post_id in (
  (select ID from wp_posts where post_title in (
    'Условия кредитования банка Тиньков',
    'Условия кредитования банка Ренессанс Капитал'
  ) and post_status = 'publish')
);

delete from wp_posts where post_title in (
  'Условия кредитования банка Тиньков',
  'Условия кредитования банка Ренессанс Капитал'
);