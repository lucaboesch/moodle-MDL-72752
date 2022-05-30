@core @core_question @javascript
Feature: A teacher can move questions between categories in the question bank
  In order to organize my questions
  As a teacher
  I move questions between categories

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1 | weeks |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    And the following "activities" exist:
      | activity   | name             | intro                   | course | idnumber |
      | qbank      | Test qbank name  | Test qbank description  | C1     | qbank1   |
    And the following "question categories" exist:
      | contextlevel    | reference | questioncategory   | name               |
      | Activity module | qbank1    | Top                | top                |
      | Activity module | qbank1    | top                | Default for qbank1 |
      | Activity module | qbank1    | Default for qbank1 | Subcategory        |
      | Activity module | qbank1    | top                | Used category      |
    And the following "questions" exist:
      | questioncategory | qtype | name                      | questiontext                  |
      | Used category    | essay | Test question to be moved | Write about whatever you want |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage

  @javascript
  Scenario: A new question category can be created
    When I follow "Test qbank name"
    And I click on "jump" "select"
    And I click on "Categories" "option"
    And I click on "Add category" "link"
    And I set the following fields to these values:
      | Name            | New Category 1    |
      | Parent category | Top               |
      | Category info   | Created as a test |
      | ID number       | newcatidnumber    |
    And I press "submitbutton"
    Then I should see "New Category 1"
    And I should see "newcatidnumber"
    And I should see "(0)"
    And I should see "Created as a test" in the "New Category 1" "list_item"

  @javascript
  Scenario: A question category can be edited
    When I follow "Test qbank name"
    And I click on "jump" "select"
    And I click on "Categories" "option"
    And I click on "Edit this category" "link" in the "Subcategory" "list_item"
    And the field "parent" matches value "&nbsp;&nbsp;&nbsp;Default for qbank1"
    And I set the following fields to these values:
      | Name            | New name     |
      | Category info   | I was edited |
    And I press "Save changes"
    Then I should see "New name"
    And I should see "I was edited" in the "New name" "list_item"

  @javascript
  Scenario: An empty question category can be deleted
    When I follow "Test qbank name"
    And I click on "jump" "select"
    And I click on "Categories" "option"
    And I click on "Delete" "link" in the "Subcategory" "list_item"
    Then I should not see "Subcategory"

  @javascript
  Scenario: An non-empty question category can be deleted if you move the contents elsewhere
    When I follow "Test qbank name"
    And I click on "jump" "select"
    And I click on "Categories" "option"
    And I click on "Delete" "link" in the "Used category" "list_item"
    And I should see "The category 'Used category' contains 1 questions"
    And I press "Save in category"
    Then I should not see "Used category"
    And I should see "Default for qbank1"

  @javascript
  Scenario: Move a question between categories via the question page
    When I navigate to "Question bank" in current page administration
    And I follow "Test qbank name"
    And I set the field "Type or select..." in the "Filter 1" "fieldset" to "Used category"
    And I click on "Apply filters" "button"
    And I should not see "Subcategory (1)" in the ".form-autocomplete-selection" "css_element"
    And I should see "Used category (1)" in the ".form-autocomplete-selection" "css_element"
    And I click on "Test question to be moved" "checkbox" in the "Test question to be moved" "table_row"
    And I click on "bulkactionsui-selector" "button"
    And I press "Move to..."
    And I set the field "id_movetocategory" to "Subcategory"
    And I press "Move to"
    Then I should see "Test question to be moved"
    And I should see "Subcategory (1)" in the ".form-autocomplete-selection" "css_element"
    And I should not see "Used category (1)" in the ".form-autocomplete-selection" "css_element"
