<?php
use DateTime;

class Clients {
  private int $id;
  private string $name;
  private string $email;
  private DateTime $created_at; 

  public function __contruct(string $name, string $email, DateTime $created_at = null){
    $this->name = $name;
    $this->email = $email;
    $this->created_at = $created_at ?? new DateTime();
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

