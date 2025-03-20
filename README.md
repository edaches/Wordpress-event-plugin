# Wisor Events Plugin Documentation

## **Overview**
The Wisor Events Plugin is a powerful WordPress plugin designed to display and manage events using **Elementor widgets** and **shortcodes**. It provides full customization options, allowing users to filter, sort, align, and style event listings to fit their website's design.

## **Installation & Activation**
### **1. Install the Plugin**
- Download the **Wisor Events Plugin** ZIP file.
- Go to **WordPress Dashboard > Plugins > Add New**.
- Click **Upload Plugin**, select the ZIP file, and click **Install Now**.
- After installation, click **Activate**.

### **2. Enable Permalinks**
- To avoid "Page Not Found" issues for event pages, go to **Settings > Permalinks** and click **Save Changes**.

## **Using the Plugin**
### **1. Elementor Widget**
To use the **Wisor Events Widget** in Elementor:
1. Open any page using **Elementor Editor**.
2. Search for **"Wisor Events"** in the widget panel.
3. Drag & drop the widget into your layout.
4. Customize the settings in the **Content & Style tabs**.
5. Click **Update** to save the changes.

#### **Elementor Widget Settings**
- **Number of Events**: Set how many events to display.
- **Event Filter**: Choose to show past, future, or all events.
- **Sort Order**: Display events in ascending or descending order.
- **Event Content Alignment**: Align event Content.
- **Description Length**: Set the character limit for event descriptions.
- **Layout Style**: Choose between **List View** or **Grid View**.

### **2. Using Shortcodes**
You can display events anywhere using the shortcode:
```html
[wisor_events limit="5" filter="all" order="ASC" align_text="left" desc_length="100"]
```
#### **Shortcode Attributes**
| Attribute       | Options (Default) | Description |
|----------------|------------------|-------------|
| `limit`        | `1 - 20` (5)      | Number of events to show. |
| `filter`       | `future, past, all` (`future`) | Show only upcoming, past, or all events. |
| `order`        | `ASC, DESC` (`ASC`) | Show oldest or newest events first. |
| `align_list`   | `left, center, right` (`left`) | Aligns the entire event list. |
| `desc_length`  | `5 - 300` (100) | Sets the maximum character length of the event description. |

### **3. Load More Button**
- The widget includes a **Load More** button to dynamically load additional events.
- Newly loaded events will match the default **List or Grid View** layout.

### **4. Infinite Scroll (Upcoming Feature)**
- Planned for a future update to enable seamless event loading without clicking "Load More".

## **Customization**
### **Custom CSS**
- Add custom styles via **Appearance > Customize > Additional CSS**.
- Example:
```css
.wisor-events-container h3 {
    color: #ff6600; /* Change event title color */
}
```

### **Template Modifications**
- Templates for events are in **/template-parts/**.
- Modify `event-list.php` and `event-grid.php` to customize event layout.

## **Frequently Asked Questions (FAQ)**
### **1. Events are not showing up?**
- Ensure you have added event posts under **Events > Add New**.
- Check **Settings > Permalinks** and click **Save Changes** to refresh URLs.

### **2. How do I change the event order?**
- Use the **Sort Order** option in the Elementor widget.
- In shortcodes, set `order="DESC"` to show the latest events first.

### **3. How do I change the number of events displayed?**
- Use the **"Number of Events"** setting in Elementor.
- In shortcodes, set `limit="10"` to display 10 events.

### **4. How do I style event descriptions?**
- Adjust the **"Description Length"** setting to limit text.
- Use **CSS** to style the event descriptions.

### **5. How do I remove "Load More"?**
- Set the **Number of Events** to a high number so all events load initially.

## **Troubleshooting**
### **1. Load More button not working?**
- Open the **browser console (`F12` > Console tab)** and check for errors.
- Ensure AJAX requests are enabled on your WordPress site.

### **2. Styles not updating?**
- Clear browser cache or try opening the page in **Incognito Mode**.
- Go to **Elementor > Tools > Regenerate CSS & Data**, then refresh.

## **Final Notes**
The Wisor Events Plugin is designed to be **lightweight, customizable, and user-friendly**. We will continue adding features such as **infinite scroll, category filters, and additional layout styles**.

🚀 **Thank you for using Wisor Events Plugin!** If you have feedback, reach out via **GitHub Issues** or **WordPress Support Forums**. Happy event managing! 🎉

