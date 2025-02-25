# Magento 2 Custom Order Processing Module

## Overview
This Magento 2 module handles custom order status updates and logging, including:
- Observers to track order status changes.
- An admin UI grid for order status logs.
- An API to update order statuses.
- Email notifications for shipped orders.

## Installation
1. Copy the `Vendor/CustomOrderProcessing` folder to `app/code/Vendor/CustomOrderProcessing/` in your Magento 2 project.
2. Run the following Magento CLI commands:
   ```bash
   bin/magento module:enable Vendor_CustomOrderProcessing
   bin/magento setup:upgrade
   bin/magento cache:flush
   bin/magento setup:di:compile
   bin/magento setup:static-content:deploy -f

## API Endpoints
Update Order Status
POST /rest/V1/order/status/update
Request Body:
{
  "orderIncrementId": "100000001",
  "newStatus": "complete"
}
Response:
{
  "message": "Order status updated successfully."
}

## Admin Grid Access
Navigate to Admin Panel -> Sales -> Order Status Log to view status changes.

## Architectural Decisions
Observer Pattern: Used to track order status changes automatically.
Custom Resource Model: Stores order status logs in a custom database table.
REST API Integration: Allows external systems to update order statuses.
Magento UI Component: Implements an admin grid for managing order logs.

