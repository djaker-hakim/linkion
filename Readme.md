# Linkion

**Linkion** is a Laravel package designed to connect the backend and the frontend in a clean, direct, and surprisingly civilized way.

Instead of shouting across HTTP endpoints all day, Linkion introduces **Linkion Components**—a structured bridge that allows frontend code to call backend logic as if they were already on speaking terms.

In simple terms:  
you write backend code in Laravel,  
you call it from the frontend,  
and everyone stays happy.

With Linkion, backend methods are exposed intentionally and safely, meaning your frontend can interact with server-side logic without the usual boilerplate, glue code, or ritual sacrifices to the API gods.

Think of Linkion as the missing link (yes, we went there) between your Laravel backend and your frontend—making them work together, not against each other.

## Why Linkion?

Modern applications rarely fail because of the backend or the frontend alone—they fail because the two refuse to cooperate.

Linkion exists to solve that problem without forcing architectural loyalty. When you use Linkion as your backend link, your frontend remains completely free. No enforced stack, no mandatory framework, no “you must use this or else.”

Whether your frontend is built with React, Vue, Svelte, or plain vanilla JavaScript, Linkion stays out of the way and simply does its job.

For developers who want a modern experience without adopting a full frontend framework, Linkion includes **Alpine.js** by default—small, reactive, and perfectly suited for server-driven applications.

Linkion also pairs naturally with **stm-ui-components**, leveraging **Blade** and **Alpine.js** to deliver reusable UI components that feel modern without sacrificing Laravel’s simplicity.

Linkion does not replace your frontend.
It empowers it—while keeping your backend exactly where it belongs.


## Installation

### Requirements

Before installing Linkion, make sure your environment meets the following requirements:

- **PHP** 8.0 or higher  
- **Laravel** 12.x

### Install via Composer

Once the requirements are satisfied, install Linkion using Composer:

```bash
composer require djaker-hakim/linkion
```

## How to Setup linkion

Setting up Linkion is deliberately minimal.

After installation, simply include the **Linkion script component** in your main layout. This script initializes the frontend bridge and enables communication with backend Linkion components.

### Example

Below is how to include the Linkion script component:

```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <x-linkion::scripts defer />

    <title>Linkion</title>
</head>
<body>

</body>
</html>
```
### linkion without alpinejs

Linkion ships with Alpinejs out of the box, providing a modern and lightweight frontend experience without requiring any additional setup.

If your project already includes Alpine.js or if you prefer not to use it you can disable Alpine loading by passing the alpine option to the script component:

```html
<x-linkion::scripts :alpine="false" defer />
```


## Creating a Linkion Component

To create a new Linkion component, use the provided Artisan command:

```bash
php artisan make:linkion counter
```

## How to Use

A Linkion component behaves very much like a **Blade component**.

To use a Linkion component, simply include it in your view using its Blade tag:

```html
<body>

    <x-counter />

</body>
```

Once a Linkion component is added, it becomes accessible from the frontend through the global `linkion` object.

There are multiple ways to access a component from the frontend.

### Option 1: Access by Component Name

You can access a Linkion component directly using its component name:

```js
document.addEventListener('linkion:ready', () => {
    console.log(linkion.counter);
});
```


If your component name contains dots, such as:

```html
<body>

    <x-dashboard.user />

</body>
```

You can access it from the frontend using camelCase notation:

```js
document.addEventListener('linkion:ready', () => {
    console.log(linkion.dashboardUser); 
});
```

> **Note**  
> When accessing components by name, conflicts may occur if multiple components share the same name. In such cases, unexpected behavior or errors may happen.  
> For applications with multiple instances of the same component, using a option 2 is recommended.

### Option 2: Access by Reference (ref)

When working with multiple instances of the same component or when dealing with long or complex component names you can provide a **ref** to your component. This ref acts as a **unique identifier** that allows you to access a specific component instance safely from the frontend.

Define your Linkion component and accept a `ref` in the constructor:

```php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Linkion\Core\LinkionComponent;

class counter extends LinkionComponent
{

    public $count;

    /**
     * Create a new component instance.
     */
    public function __construct($ref)
    {
        $this->ref = $ref;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return $this->component('components.counter');
    }
}
```
When rendering multiple instances, provide a unique ref for each:

```html
<x-counter ref="mainCounter" />
<x-counter ref="secondaryCounter" />
```
Now you can access each component instance unambiguously using the ref:

```js
console.log(linkion.mainCounter);      // Access mainCounter
console.log(linkion.secondaryCounter); // Access secondaryCounter
```

### Using Linkion Components with Alpine.js

When using **Alpine.js**, Linkion provides a **magic `$lnkn` object** inside your component.  
This allows your frontend Alpine instance to directly interact with the Linkion component’s backend state and methods.

```html
@lnknComponent
    <div x-data="{count: null}" x-init="count = $lnkn.count">
        
        <div id="counter" x-text="count"></div>

    </div>
@endlnknComponent
```
## Calling Backend Methods from the Frontend

Linkion allows you to call backend methods directly from the frontend—no API endpoints, no extra requests, just plain direct interaction.

### Example: Backend Method

```php
public function increase()
{
    $this->count += 1;
}
```
### Calling from Vanilla JavaScript

If you are not using Alpine, you can call the method directly via the global `linkion` object:

```js
linkion.counter.increase()
```
### Calling from Alpine

When using Alpine, the magic `$lnkn` object gives you access to the same backend methods inside your Linkion component:

```html
@lnknComponent
    <div x-data="counter()">
        <div id="counter" x-text="count"></div>
        <button class="btn" x-on:click="$lnkn.increase()">+</button>
    </div>
    <script @lnknAsset>
        const counter = () => {
            return {
                count: 0,
                init(){
                    this.count = this.$lnkn.count;
                    this.$lnkn.watch('count', () => this.count = this.$lnkn.count);
                }
            }
        } 
    </script>
@endlnknComponent
```

> **Note**  
> Only **public properties** and **public methods** of a Linkion component are accessible from the frontend.  
> Private or protected properties and methods cannot be accessed, ensuring encapsulation and security.

## Uploading Files

Linkion makes uploading files from the frontend simple and straightforward. You can upload single or multiple files directly to your Linkion component.

### Vanilla JavaScript Example

Single file upload:

```html
@lnknComponent
<div>     
    <div id="user" x-text="user"></div>
    
    <input type="file" >
</div>

<script @lnknScript>
    const fileInput = document.querySelector('[type=file]');
    fileInput.addEventListener('change', () => {
        // single file upload
        linkion.user.upload('photo', e.target.files[0]);

        // Multiple files upload
        linkion.user.upload('photo', e.target.files[0]);
    });
</script>
@endlnknComponent
```
### Using Alpine.js

Inside an Alpine component, use the magic $lnkn object:

```html
@lnknComponent
    <div x-data>
        ...
        <input type="file" x-on:change="$lnkn.upload('photo',$event.target.files)">
    </div>
@endlnknComponent
```

In your Linkion component, define a public property to hold the uploaded file and handle it in a method:

```php
public $photo;

public function save()
{
    // Store the uploaded file with default generated name
    $this->photo->store('images');

    // Store the uploaded file with a custom name
    $this->photo->storeAs('images', 'user_photo.jpg');
}
```

> **Note**  
> Only **public properties** are accessible from the frontend. The `$photo` property must be public for uploads to work.  
> For more information about file storage in Laravel, check out the [complete file storage documentation](https://laravel.com/docs/12.x/filesystem).


### Upload Progress Tracking

Linkion also provides access to **upload progress events**, allowing you to track file upload status in real time.

During a file upload, Linkion emits an event named:

**upload-progress**


The event `detail` contains useful information for tracking uploads:

- `progress` – Upload progress percentage  
- `componentName` – The name of the Linkion component  
- `ref` – The component reference (if provided)

This makes it easy to track upload progress across multiple components or target a specific component instance when multiple uploads are happening simultaneously.

#### Example

```js
window.addEventListener('upload-progress', (e) => {
    const { progress, componentName, ref } = e.detail;

    console.log(progress);        // Upload progress percentage
    console.log(componentName);   // Component name
    console.log(ref);             // Component ref (if any)
});
```


## Rendering Modes

Linkion supports **two rendering modes**, giving you full control over performance, memory usage, and developer experience:

- **CSR (Client-Side Rendering)**
- **SSR (Server-Side Rendering)**

Linkion does **not lock you into a single rendering strategy**. You choose what fits your use case.

---

### Client-Side Rendering (CSR)

By default, Linkion renders components using **Client-Side Rendering (CSR)**.

In CSR mode, rendering and state updates are handled by the frontend. For best performance, you should **avoid rendering backend variables directly in Blade**, such as:

```blade
{{ $count }}
```

Instead, use frontend bindings.

#### Using Vanilla JavaScript

```js
document.querySelector('#counter').textContent = linkion.counter.count;
```

#### Using Alpine.js

```html
<div x-text="$lnkn.count"></div>
```

By following this approach:

- Rendering is faster

- Frontend updates are reactive

- Unnecessary backend re-renders are avoided

CSR is ideal for interactive and frequently changing UI.

### Server-Side Rendering (SSR)

In Server-Side Rendering (SSR) mode, Blade is responsible for rendering the component.

This is useful when:

- The data does not change frequently

- You want to reduce memory usage on the client

- Interactivity is minimal

To enable SSR-style rendering behavior, add the following property to your Linkion component:
```php
public $componentCached = false;
```

### Rule of Thumb

- Linkion supports both CSR and SSR

- Use Alpine.js or JavaScript for CSR

- Use Blade directives for SSR

- Choose based on performance and interactivity needs

Linkion does not force a rendering strategy you stay in control.

## Nested Components Behavior

Rendering behavior differs when components are nested.

### SSR Nested Components

In SSR mode, nested components **do not preserve state**.

Each time a parent component is re-rendered, all nested components are rebuilt from scratch—similar to a full server-side re-render.

### CSR Nested Components

In CSR mode, nested components are managed by the frontend.

Their state remains intact, even when parent components update, resulting in a smoother and more efficient user experience.

## Ghost Components

Ghost components are Linkion components that exist in the frontend but are **not visible in the DOM**.

Just like a ghost, they exist… but they have no body.

These components are fully functional:
- They are registered on the frontend
- Their backend logic is accessible
- Their methods and properties can be called

The only difference is that they do not render any visible HTML output.

Ghost components are useful for:
- Background logic
- State management
- Shared functionality
- Non-visual backend interaction

They allow you to leverage Linkion’s power without introducing unnecessary DOM elements.

## Creating a Ghost Component

A Ghost Component is created when a Linkion component is loaded on the frontend **without being rendered in the DOM**.

In this case, the component exists entirely in memory—accessible, interactive, but invisible.

---

### Loading a Ghost Component from the Frontend

You can create a ghost component using the `load` method:

```js
linkion.load('counter', { ref: 'mainCounter', count: 1 });
```

This loads the counter component on the frontend, assigns it attributes, and passes initial data to the backend—without rendering any HTML.

### Backend Component Example

Your Linkion component can receive the passed values through its constructor:

```php
public $count;

/**
 * Create a new component instance.
 */
public function __construct($ref, $count)
{
    $this->ref = $ref;
    $this->count = $count;
}
/**
 * Get the view / contents that represent the component.
 */
public function render(): View|Closure|string
{
    return "";
}
```
### Accessing the Ghost Component

Even though the component is not visible in the DOM, it is fully accessible:
```js
linkion.mainCounter.count; // Output: 1
```
The component behaves like any other Linkion component it simply has no visual representation.

### Loading vs Rendering Components

The `load` method does more than create ghost components.

When a component is loaded using `load`, Linkion initializes the component **even if it has a template**, but the template is **not rendered** immediately.

In other words, the component is:
- Created and initialized
- Available on the frontend
- Fully functional
- Not rendered in the DOM

To render a previously loaded component, you must explicitly call the `render` method.  
When rendered this way, the component is rendered in **Client-Side Rendering (CSR)** mode.

```js
linkion.load('counter', { ref: 'mainCounter' });

// Later...
linkion.mainCounter.render({}, el);
```

## Reactivity with `onUpdate`

If you need reactivity in Linkion, you can listen for component updates using the `onUpdate` method.

The `onUpdate` callback is triggered whenever the component state is updated, allowing you to react to changes without manually polling or wiring complex events.

---

### Using Vanilla JavaScript

You can register an update listener directly on the component:

```js
linkion.counter.onUpdate((props) => {
    console.log(props.count);
});
```

The callback receives the updated component instance, giving you access to all public properties.

### Using Alpine.js

When using Alpine.js, the magic `$lnkn` object provides the same capability:

```js
$lnkn.onUpdate((props) => {
    console.log(props.count);
});
```
This makes it easy to keep Alpine state, UI, or side effects in sync with backend updates.

## Watching Specific Properties

If you need more granular reactivity, Linkion allows you to watch **specific component properties** using the `watch` method.

Instead of reacting to every component update, you can listen only to changes on a single property.

---

### Using Vanilla JavaScript

```js
linkion.counter.watch('count', (value) => {
    console.log(value);
});
```
The callback is triggered whenever the count property changes and receives the updated value.

### Using Alpine.js

When using Alpine.js, the same functionality is available through the magic `$lnkn` object:

```js
$lnkn.watch('count', (value) => {
    console.log(value);
});
```

## Blade Directives

Linkion provides several Blade directives to define component boundaries and manage scripts correctly.

---

### `@lnknComponent`

The `@lnknComponent` directive defines the **scope of a Linkion component**.

Everything between:

```blade
@lnknComponent
    ...
@endlnknComponent
```
is considered part of the Linkion component.
Any markup or scripts outside this block are not associated with the component.

This directive is required when using Alpine.js or when interacting with the component through `$lnkn`.

### `@lnknAsset`

The `@lnknAsset` directive is used as a script attribute.

Scripts marked with `@lnknAsset` are rendered before the component body, making them ideal for:

- Declaring helper functions
- Defining Alpine data
- Preparing logic required by the component

```html
<script @lnknAsset>
    // Code available before component render
</script>
```

### `@lnknScript`

The `@lnknScript` directive is also used as a script attribute.

Scripts marked with `@lnknScript` are rendered after the component body, making them suitable for:

- DOM-dependent logic
- Post-render initialization
- Side effects that require the component to exist in the DOM

```html
<script @lnknScript>
    // Code executed after component render
</script>
```

### `@lnknScript` vs `@lnknScript`

| Directive     | Render Timing         | Use Case                           |
|---------------|-----------------------|------------------------------------|
| `@lnknAsset`  | Before component body | Setup logic, helpers, Alpine data  |
| `@lnknScript` | After component body  | DOM access, post-render logic      |

## Events

Linkion supports **two-way event communication**:
- Backend → Frontend
- Frontend → Backend

---

### Backend to Frontend Events

You can dispatch events from the backend to the frontend using the `dispatch` method.

#### Backend

```php
$detail = ['name' => 'hakim']
$this->dispatch('event', $detail);
```

#### Frontend Listener

Frontend events behave like standard `CustomEvents`.
You can listen to them using the `document` or `window` object.

```js
document.addEventListener('event', (e) => {
    console.log(e.detail);
});
```

### Frontend to Backend Events

To send an event from the frontend to the backend, use the linkion.dispatch method.

#### Frontend

```js
linkion.dispatch('event', {name: 'hakim'});
```

#### Backend Listener

You can listen to frontend-dispatched events in the backend by using the `#[On]` attribute.

use Linkion\Attributes\On;

```php
#[On('event')]
public function handleEvent($detail)
{
    // Handle event data
}
```

## Middleware

Linkion components support Laravel middleware in the same way as controllers.  
Middleware is registered inside the component constructor and can be applied globally, to specific methods, or excluded from specific methods.

---

### Defining Middleware

Middleware is registered in the component constructor:

```php
use Linkion\Component;

class Dashboard extends Component
{
    public function __construct()
    {
        $this->middleware('auth');
    }
}
```
This middleware will run for all public methods of the component.

### Multiple Middleware

```php
public function __construct()
{
    $this->middleware('auth');
    $this->middleware('verified');
}
```
### Middleware With Parameters

```php
public function __construct()
{
    $this->middleware('role:admin');
}
```


### Middleware for Specific Methods (Only)
Apply middleware to only specific methods:

```php
public function __construct()
{
    $this->middleware('auth');
    $this->middleware('verified');
}
```

### Middleware Except Specific Methods
Exclude middleware from specific methods:
```php
public function __construct()
{
    $this->middleware('auth');
    $this->middleware('verified');
}
```

### Combining `only` and `except`

```php
public function __construct()
{
    $this->middleware('auth')
        ->only(['store', 'update'])
        ->except(['index']);
}
```

## Testing

Linkion provides a **Component Tester** that allows you to test the **backend functionality** of Linkion components in isolation.

The component tester focuses on:
- Public properties
- Public methods
- Component state changes
- Backend logic (without requiring frontend rendering)

This makes it easy to validate component behavior in a clean and predictable way, similar to testing Laravel controllers or Livewire components.

---

### What You Can Test

Using the Linkion component tester, you can:

- Call public component methods
- Verify logic without involving the frontend

---

### Scope of Testing

- ✅ Backend logic  
- ✅ Public methods  
- ✅ Public properties

The goal is to ensure your component’s **server-side behavior** works as expected before integrating it with the frontend.

#### Example Component

```php
use Linkion\Core\LinkionComponent;


class Counter extends LinkionComponent
{
    public $count;
    
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        
    }

    public function increase(){
        $this->count += 1;
    }
}
```

#### Example Test

```php
use App\View\Components\counter;
use Linkion\Testing\Linkion;


 public function test_Counter(): void
{
    
    $result = Linkion::test(Counter::class)
    ->setProperty('count', 1)
    ->runSilently('increase')
    ->getProperty('count');
    
    $this->assertEquals(2, $result);          
}
```
### Available Testing Methods

| Method | Description |
|-------|-------------|
| `Linkion::test($component, $args = [])` | Create a new Linkion component test instance |
| `setProperty($name, $value)` | Set a single public property |
| `setProperties(array $properties)` | Set multiple public properties |
| `getProperty($name)` | Get a single public property value |
| `getProperties()` | Get all public properties |
| `run($method, $args = [])` | Run a method and return its result |
| `runSilently($method, $args = [])` | Run a method and return the test instance for chaining |



## Linkion in Production

To make Linkion more performant in production, you can **cache components** so they are not rebuilt on every request.

Caching improves:
- Response time
- Server performance
- Overall application scalability

---

### Caching Linkion Components

To cache all Linkion components, run:

```bash
php artisan linkion:cache
```
This command builds and stores the component metadata so Linkion can load them quickly without reprocessing on each request.

### Clearing the Cache

If you update components, add new ones, or change configuration, you should clear the cache:

```bash
php artisan linkion:clear
```
This forces Linkion to rebuild components on the next request.

## Conclusion

Linkion bridges the gap between backend and frontend in a clean, flexible, and developer-friendly way.

It allows you to:
- Call backend logic directly from the frontend
- Stay framework-agnostic on the frontend (Vanilla JS, Alpine.js, or anything else)
- Choose freely between **CSR** and **SSR** rendering strategies
- Manage state, events, uploads, and reactivity with minimal boilerplate
- Scale safely with middleware, testing utilities, and production caching

Whether you’re building small interactive components or complex applications, Linkion adapts to your architecture instead of forcing one.  
It doesn’t lock you into a rendering mode, a frontend framework, or a workflow—you decide what fits best.

In short: **Linkion links things without getting in your way.**
