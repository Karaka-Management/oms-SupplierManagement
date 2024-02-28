# Attributes

## Default

The module automatically installs the following default attributes which can be set in the attribute tab in the respective supplier.

### General

| Attribute | Description | Internal default value |
| --------- | ----------- | ---------------------- |
| abc_class | Custom supplier rating | |
| support_emails | Send email for support ticket changes | yes |
| support_email_address | Email address for support tickets | Account email |
| legal_form | Supplier legal form | |

### Categories

Suppliers can be put in categories for horizontal and vertical grouping. By default the system uses segment->section->supplier_group as categories as well as supplier_type. These categories also get used by other modules. Additional groups can be defined but are not used by other modules by default.

| Attribute | Description | Internal default value |
| --------- | ----------- | ---------------------- |
| segment | Level 1 | 1 |
| section | Level 2 | 1 |
| supplier_group | Level 3 | 1 |
| supplier_type | **NOT** hierarchically. | 1 |
| supplier_area | **NOT** hierarchically. Area a supplier belongs to. Useful for grouping suppliers based on location or procurement clerk. | 1 |

| Level | >                     | >                     | >                     | >                     | >                     | >                     | Sample                |
| :---: | :-------------------: | :-------------------: | :-------------------: | :-------------------: | :-------------------: | :-------------------: | :-------------------: |
| 1     | >                     | >                     | >                     | >                     | Segment 1             | >                     | Segment 2             |
| 2     | >                     | >                     | Section 1.1           | >                     | Section 1.2           | >                     | Section 2.1           |
| 3     | Supplier Group 1.1.1  | >                     | Supplier Group 1.1.2  | >                     | Supplier Group 1.2.1  | Supplier Group 2.1.1  | Supplier Group 2.1.2  |

> You could consider the supplier (number) itself `Level 4`.

### Billing

| Attribute | Description | Internal default value |
| --------- | ----------- | ---------------------- |
| bill_emails | Should bills get emailed to the customer | yes |
| bill_email_address | Email address used for sending bill via email | account email |
| bill_language | Language of the bill | Account language -> default bill language |
| bill_currency | Currency of the bill. Coming soon. | |
| bill_match_pattern | Regex pattern for matching a supplier against their bill. Usually a tax id, bank account or otherwise unique string used in the bills from the supplier. | |
| bill_date_format | Date format used by the supplier | |

### Purchase & Stock

| Attribute | Description | Internal default value |
| --------- | ----------- | ---------------------- |
| minimum_order | Minimum order amount required | |

### Accounting

| Attribute | Description | Internal default value |
| --------- | ----------- | ---------------------- |
| purchase_tax_code | Tax code for purchase | |
| vat_id | VAT id for european customers | |
| tax_id | Tax id for local tax id | |
| line_of_credit | Maximum amount allowed to be purchased taking unpaid invoices into account | |
| credit_rating | Credit rating | |