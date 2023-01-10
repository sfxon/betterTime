# Access control

Access control describes, which user can use which part of a software.
BetterTime uses access control on two levels:

1. User Level
2. Team Level

The relation between the two parts is defined like this:

* A **User** can access his own private data.
* A **User** can be assigend to a team.
* A **Team** has access to team data.
* The relation between a **User** and a **Team** is defined by a **Role**.
* A **Role** defines, *which* parts of an application can be used.
* A **Role** defines, *how* parts of an application can be used.


## 1. User Level

### 2. a) Data-Relation on the User Level

On the user level, we talk about data, that directly belongs to a user.
Access Control is done, by adding UserId properties to entities.
When data is queried, it should always check for the UserId, too.
Keep in mind, that you might want to add a property for the TeamId, too.

A user can query all of his own data.


## 2. Team Level

### 2.a) Team Level: Role and ACL

Different to the *User Level*, on the **Team Level** there might be additional restrictions about *what data can be access*
and *the way data can be accessed*. These restrictions define, what rights a team member has in general, and does not affect the Data-Relation.

BetterTime uses **Roles** to accomplish this.
The relation of a **User** to his **Team** is defined by a **Role**.
A **Role** is a unique entity.
Each **Role** uses an **ACL** (Access Control List).
**ACLs´** define, which part of the application can be accessed with which **Access Rights**.
There can be multiple Access Rights, at least for CRUD Operations: Create, Read, Update, Delete.


### 2.b) Data-Relation on the Team Level

Data-Relation is done, by adding a TeamId property to a data Entity.
If an assignment on a team-level is done, the data belongs to a team.
Usually, data belongs to a user and a team.
The assignment to the user is defined by the user, whom created the data.
Usually, the assignment to the user will not change.
The assignment to a team, on the other side, can change over time. 
The assignment to a team is more loose than the assignment to a team, one could say: optional.
Teams are used, to allow multiple users access to the same data.
