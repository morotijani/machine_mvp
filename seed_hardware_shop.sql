-- Hardware & Wood Shop Seed Data
-- Instructions: You can import this file directly into your database using phpMyAdmin, 
-- or run it via terminal to populate your `items` table with sample data for your new client.

-- 1. Insert Categories (If you have a categories table, though currently it seems categories are just strings in the items table)
-- We will just use these category names in the items insert: 'Lumber & Wood', 'Doors & Gates', 'Fasteners', 'Hardware & Security'

-- 2. Insert Standard Items
INSERT INTO `items` (`name`, `type`, `category`, `sku`, `unit`, `price`, `cost_price`, `quantity`, `location`, `image_path`) VALUES
-- Lumber & Wood
('Mahogany Wood Board 2x4x8', 'standard', 'Lumber & Wood', 'WD-MAH-001', 'pcs', 150.00, 90.00, 200, 'Aisle 1', ''),
('Odum Wood Board 1x12x10', 'standard', 'Lumber & Wood', 'WD-ODU-002', 'pcs', 120.00, 75.00, 150, 'Aisle 1', ''),
('Wawa Plywood 1/2 inch (4x8)', 'standard', 'Lumber & Wood', 'WD-PLY-003', 'sheet', 85.00, 50.00, 300, 'Aisle 2', ''),
('Treated Teak Post 4x4x10', 'standard', 'Lumber & Wood', 'WD-TEK-004', 'pcs', 210.00, 130.00, 80, 'Aisle 1', ''),

-- Doors & Gates
('Solid Panel Mahogany Door (Standard)', 'standard', 'Doors & Gates', 'DR-MAH-001', 'pcs', 1200.00, 700.00, 25, 'Showroom A', ''),
('Flush Door (Interior) 32x80', 'standard', 'Doors & Gates', 'DR-INT-002', 'pcs', 350.00, 200.00, 50, 'Showroom A', ''),
('Steel Security Gate (Single)', 'standard', 'Doors & Gates', 'GT-STL-001', 'pcs', 2500.00, 1500.00, 10, 'Showroom B', ''),
('Wooden Driveway Gate (Double)', 'standard', 'Doors & Gates', 'GT-WD-002', 'pcs', 4500.00, 2800.00, 5, 'Showroom B', ''),

-- Fasteners (Nails & Screws)
('Concrete Nails (2 inch)', 'standard', 'Fasteners', 'FN-CN-02', 'box', 45.00, 25.00, 500, 'Aisle 3', ''),
('Roofing Nails (Umbrella Head)', 'standard', 'Fasteners', 'FN-RN-01', 'box', 60.00, 35.00, 400, 'Aisle 3', ''),
('Wood Screws 1.5 inch', 'standard', 'Fasteners', 'FN-WS-15', 'pack', 25.00, 12.00, 800, 'Aisle 3', ''),
('Drywall Screws 2 inch', 'standard', 'Fasteners', 'FN-DS-02', 'pack', 30.00, 15.00, 600, 'Aisle 3', ''),

-- Hardware & Security
('Heavy Duty Padlock (Brass)', 'standard', 'Hardware & Security', 'HD-PDL-01', 'pcs', 120.00, 70.00, 150, 'Aisle 4', ''),
('Mortise Door Lock (Complete Set)', 'standard', 'Hardware & Security', 'HD-MLK-02', 'set', 250.00, 140.00, 80, 'Aisle 4', ''),
('Stainless Steel Door Hinge (4 inch)', 'standard', 'Hardware & Security', 'HD-HNG-04', 'pair', 40.00, 20.00, 300, 'Aisle 4', ''),
('Gate Barrel Bolt (6 inch)', 'standard', 'Hardware & Security', 'HD-BLT-06', 'pcs', 35.00, 18.00, 200, 'Aisle 4', ''),
('Wood Glue (1 Gallon)', 'standard', 'Hardware & Security', 'HD-GLU-01', 'gal', 95.00, 55.00, 100, 'Aisle 5', ''),
('Sandpaper Assorted Pack', 'standard', 'Hardware & Security', 'HD-SND-01', 'pack', 15.00, 5.00, 400, 'Aisle 5', '');

-- 3. Insert a Bundle (e.g., A complete Door Installation Kit)
-- Note: Assuming IDs generated above start around ID 100 for safety, but since we don't know the exact IDs, 
-- it's safer to just insert the bundle item, and the user can manually link the components via the app UI.
INSERT INTO `items` (`name`, `type`, `category`, `sku`, `unit`, `price`, `cost_price`, `quantity`, `location`, `image_path`) VALUES
('Complete Interior Door Kit', 'bundle', 'Doors & Gates', 'BND-IDK-01', 'bundle', 450.00, 310.00, 20, 'Showroom A', ''),
('Gate Installation Hardware Pack', 'bundle', 'Hardware & Security', 'BND-GHP-01', 'bundle', 280.00, 160.00, 30, 'Aisle 4', '');
