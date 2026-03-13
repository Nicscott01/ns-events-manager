# NS Events Manager

A lightweight WordPress plugin for managing event listings as a custom post type. Built for sites that promote events hosted elsewhere — each event links out to an external registration or detail page rather than having its own WordPress post page.

Optimized for use with [Breakdance](https://breakdance.com/) post loop builders, but usable with any page builder that supports custom post type queries.

---

## Features

- **Custom post type** (`ns_event`) with a configurable admin menu label, slug, and icon
- **External URL redirect** — visitors hitting a single event URL are immediately redirected (301) to the event's external registration page
- **Advanced Custom Fields integration** — all event metadata is managed through ACF field groups, auto-registered by the plugin
- **Configurable fields** — toggle optional fields (end date, venue, full description, capacity, featured flag) on or off from the settings page
- **Sortable admin columns** — event list in wp-admin shows date, type, location, and external URL; sortable by event date (soonest first by default)
- **REST API support** — core event meta fields exposed via the WP REST API (toggleable)
- **WP-CLI seed script** — quickly populate sample events for development and testing

---

## Requirements

| Requirement | Version |
|-------------|---------|
| PHP | >= 8.0 |
| WordPress | >= 5.0 |
| [Advanced Custom Fields (ACF)](https://www.advancedcustomfields.com/) | Free or Pro |

ACF is required. The plugin will display an admin notice if ACF is not active.

---

## Installation

1. Clone or download this repository into your WordPress plugins directory:

   ```bash
   cd wp-content/plugins
   git clone https://github.com/your-username/ns-events-manager.git
   ```

2. Install and activate [Advanced Custom Fields](https://wordpress.org/plugins/advanced-custom-fields/) if you haven't already.

3. Activate **NS Events Manager** from the WordPress admin Plugins screen.

4. Configure the plugin at **Settings > Events Manager**.

---

## Configuration

Navigate to **Settings > Events Manager** to configure the plugin.

### General Settings

| Setting | Default | Description |
|---------|---------|-------------|
| Menu Label | Events | Label shown in the wp-admin sidebar menu |
| Singular Label | Event | Singular form used in admin UI strings |
| Plural Label | Events | Plural form used in admin UI strings |
| URL Slug | `events` | Base slug for the CPT rewrite rules |
| Menu Icon | `dashicons-calendar-alt` | Any [Dashicon](https://developer.wordpress.org/resource/dashicons/) slug |
| Enable REST API | On | Exposes events and core meta in the WP REST API |
| Default Link Label | Learn More | Fallback button text when an event has no custom link label |

> Changing the URL slug will automatically flush WordPress rewrite rules.

### Optional Fields

Enable additional ACF fields per your use case:

| Field | Description |
|-------|-------------|
| End Date | For multi-day events |
| Venue Name | Specific venue or location name |
| Full Description | Rich-text WYSIWYG editor (in addition to the excerpt) |
| Capacity | Maximum number of attendees |
| Featured | Boolean flag to mark highlighted events |

---

## Event Fields

Every event includes these core fields (managed via ACF):

| Field | Type | Notes |
|-------|------|-------|
| Event Date | Date picker | Required; stored in `Ymd` format |
| Start Time | Time picker | Stored as `H:i:s`; displayed in 12-hour format |
| End Time | Time picker | Optional |
| Event Type | Dropdown | Speaking, Retreat, Workshop, Online, Other |
| Location | Text | e.g. "Austin, TX" or "Online" |
| Price | Text | Flexible — supports any pricing display string |
| External URL | URL | Optional; where visitors are redirected when set |
| Link Label | Text | Custom button text; falls back to the default set in settings |
| RSVP Email | Email | Optional RSVP contact address |
| RSVP Email Subject | Text | Optional pre-filled subject line |
| RSVP Email Body | Textarea | Optional pre-filled message body |

---

## How the Redirect Works

Single event post URLs (e.g. `yoursite.com/events/my-event/`) redirect visitors directly to the event's **External URL** via an HTTP 301 redirect. If no external URL is set, visitors are redirected to the home page.

This means your events act as a curated directory pointing to external registration or detail pages (Eventbrite, Luma, a separate registration site, etc.) rather than hosting duplicate content.

The CPT remains `publicly_queryable` so post loop builders like Breakdance can render event cards in archive/loop views.

---

## Displaying Events

The plugin does not include shortcodes or block templates. Instead, use your page builder's post loop builder to query and display `ns_event` posts.

**Recommended approach with Breakdance:**

1. Add a Post Loop Builder element to your page
2. Set the post type to `Events` (or whatever label you configured)
3. Add custom field bindings for event date, location, type, and external URL
4. Sort by `event_date` meta key ascending to show upcoming events first

The following meta keys are available for use in post loop templates:

- `event_date` — date in `Ymd` format
- `event_type` — event type string
- `event_location` — location text
- `external_url` — the external link URL
- `rsvp_email` — RSVP email address
- `rsvp_subject` — pre-filled RSVP email subject
- `rsvp_body` — pre-filled RSVP email body

---

## REST API

When REST API support is enabled, events are available at:

```
GET /wp-json/wp/v2/ns_event
```

The following meta fields are included in REST responses:

- `event_date`
- `event_type`
- `event_location`
- `external_url`
- `rsvp_email`
- `rsvp_subject`
- `rsvp_body`

---

## WP-CLI Seed Script

To populate sample events for development:

```bash
wp eval-file wp-content/plugins/ns-events-manager/seed-events.php
```

To remove seeded events:

```bash
wp eval-file wp-content/plugins/ns-events-manager/seed-events.php remove
```

---

## License

GPL-2.0-or-later. See [LICENSE](https://www.gnu.org/licenses/gpl-2.0.html).
