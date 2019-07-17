<?php

// User Types - WARNING: MAXIMUM 10 CHARACTERS
const USER_ROLE_SUPERADMIN  = 'SUPERADMIN';
const USER_ROLE_AUTHOR = 'AUTHOR';
const USER_ROLE_READER = 'READER';
const USER_ROLES = array(USER_ROLE_SUPERADMIN, USER_ROLE_AUTHOR, USER_ROLE_READER);

// Series states
const SERIE_STATE_DRAFT = 'draft';
const SERIE_STATE_ACTIVE = 'active';
const SERIE_STATE_PAUSED = 'paused';
const SERIE_STATE_CANCELED = 'canceled';
const SERIE_STATE_COMPLETED = 'completed';
const SERIES_STATES = array(SERIE_STATE_DRAFT, SERIE_STATE_ACTIVE, SERIE_STATE_PAUSED, SERIE_STATE_CANCELED, SERIE_STATE_COMPLETED);

// Pagination
const RESULTS_PER_PAGE = 8;

// Chapters
const COMIC_CHAPTER_MAX_PAGES = 30;

// WARNING: CHANGING IMAGE SIZES CAN BE VERY DANGEROUS

// Image sizes - Comics
const COMIC_COVER_WIDTH = 900;
const COMIC_COVER_HEIGHT = 1200;
const COMIC_BANNER_WIDTH = 1920;
const COMIC_BANNER_HEIGHT = 480;

// Image sizes - Comic chapters
const COMIC_CHAPTER_THUMBNAIL_WIDTH = 540;
const COMIC_CHAPTER_THUMBNAIL_HEIGHT = 240;
const COMIC_PAGE_MAX_WIDTH = 940;
const COMIC_PAGE_MAX_HEIGHT = 4000;

// Image sizes - Profile picture
const AVATAR_WIDTH = 200;
const AVATAR_HEIGHT = 200;
