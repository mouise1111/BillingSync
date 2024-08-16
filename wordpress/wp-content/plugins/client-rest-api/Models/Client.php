<?php
use DateTime;

class Client {
  private int $id;
  private string $name;
  private string $email;
  private DateTime $created_at; 
  private string $custom_1;
  private string $birthday;

  public function __construct(string $name, string $email, DateTime $created_at = null, string $custom_1 = null, string $birthday){
    $this->name = $name;
    $this->email = $email;
    $this->created_at = $created_at ?? new DateTime();
    $this->custom_1 = $custom_1 ?? uniqid('w_'); 
    // if we are creating a user from Fossbilling, then we get the custom_1 value from the queue, otherwise we will set the value here
    $this->birthday = $birthday;
  }
  
  public function getDateOfBirth(): string {
    return $this->birthday;
  } 
  
  public function setDateOfBirth(): void {
    $this->birthday = $birthday;
  }
  public function getCustom1() {
    return $this->custom_1;
  }

  public function setCustom1($custom_1) {
    $this->custom_1 = $custom_1;
  }
 
 // Getter method for 'id'
  public function getId(): int {
    return $this->id;
  }

  // Setter method for 'id' (used when retrieving from database)
  public function setId(int $id): void {
    $this->id = $id;
  }

  public function setName(string $name): void {
    $this->name = $name; 
  }

  public function getName(): string {
    return $this->name;
  }

  public function setEmail(string $email): void {
    $this->email = $email;
  }

  public function getEmail(): string {
    return $this->email;
  }

  public function setCreatedAt(DateTime $created_at): void {
    $this->created_at = $created_at;
  }

  public function getCreatedAt() : DateTime {
    return $this->created_at;
  }
}

